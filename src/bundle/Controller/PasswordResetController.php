<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUserBundle\Controller;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\Values\User\User;
use EzSystems\EzPlatformUser\Form\Data\UserPasswordResetData;
use EzSystems\EzPlatformUser\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface;
use EzSystems\EzPlatformUser\View\ForgotPassword\FormView;
use EzSystems\EzPlatformUser\View\ForgotPassword\LoginView;
use EzSystems\EzPlatformUser\View\ForgotPassword\SuccessView;
use EzSystems\EzPlatformUser\View\ResetPassword\InvalidLinkView;
use EzSystems\EzPlatformUser\View\ResetPassword\FormView as UserResetPasswordFormView;
use EzSystems\EzPlatformUser\View\ResetPassword\SuccessView as UserResetPasswordSuccessView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\User\UserTokenUpdateStruct;
use Swift_Mailer;
use DateTime;
use DateInterval;
use Swift_Message;
use Twig\Environment;

class PasswordResetController extends Controller
{
    /** @var \EzSystems\EzPlatformUser\Form\Factory\FormFactory */
    private $formFactory;

    /** @var \eZ\Publish\API\Repository\UserService */
    private $userService;

    /** @var Swift_Mailer */
    private $mailer;

    /** @var \Twig\Environment */
    private $twig;

    /** @var string */
    private $tokenIntervalSpec;

    /** @var \EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface */
    private $notificationHandler;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var string */
    private $forgotPasswordMail;

    /**
     * @param \EzSystems\EzPlatformUser\Form\Factory\FormFactory $formFactory
     * @param \eZ\Publish\API\Repository\UserService $userService
     * @param Swift_Mailer $mailer
     * @param \Twig\Environment $twig
     * @param \EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface $notificationHandler
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     * @param string $tokenIntervalSpec
     * @param string $forgotPasswordMail
     */
    public function __construct(
        FormFactory $formFactory,
        UserService $userService,
        Swift_Mailer $mailer,
        Environment $twig,
        TranslatableNotificationHandlerInterface $notificationHandler,
        PermissionResolver $permissionResolver,
        string $tokenIntervalSpec,
        string $forgotPasswordMail
    ) {
        $this->formFactory = $formFactory;
        $this->userService = $userService;
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->notificationHandler = $notificationHandler;
        $this->permissionResolver = $permissionResolver;
        $this->tokenIntervalSpec = $tokenIntervalSpec;
        $this->forgotPasswordMail = $forgotPasswordMail;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \EzSystems\EzPlatformUser\View\ForgotPassword\FormView|\EzSystems\EzPlatformUser\View\ForgotPassword\SuccessView|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     */
    public function userForgotPasswordAction(Request $request)
    {
        $form = $this->formFactory->forgotUserPassword();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $users = $this->userService->loadUsersByEmail($data->getEmail());

            /** Because is is possible to have multiple user accounts with same email address we must gain a user login. */
            if (\count($users) > 1) {
                return $this->redirectToRoute('ezplatform.user.forgot_password.login');
            }

            if (!empty($users)) {
                $user = reset($users);
                $token = $this->updateUserToken($user);

                $this->sendResetPasswordMessage($user->email, $token);
            }

            return new SuccessView(null);
        }

        return new FormView(null, [
            'form_forgot_user_password' => $form->createView(),
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \EzSystems\EzPlatformUser\View\ForgotPassword\LoginView|\EzSystems\EzPlatformUser\View\ForgotPassword\SuccessView
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     */
    public function userForgotPasswordLoginAction(Request $request)
    {
        $form = $this->formFactory->forgotUserPasswordWithLogin();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $user = $this->userService->loadUserByLogin($data->getLogin());
            } catch (NotFoundException $e) {
                $user = null;
            }

            if (!$user || \count($this->userService->loadUsersByEmail($user->email)) < 2) {
                return new SuccessView(null);
            }

            $token = $this->updateUserToken($user);
            $this->sendResetPasswordMessage($user->email, $token);

            return new SuccessView(null);
        }

        return new LoginView(null, [
            'form_forgot_user_password_with_login' => $form->createView(),
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $hashKey
     *
     * @return \EzSystems\EzPlatformUser\View\ResetPassword\FormView|\EzSystems\EzPlatformUser\View\ResetPassword\InvalidLinkView|\EzSystems\EzPlatformUser\View\ResetPassword\SuccessView
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     */
    public function userResetPasswordAction(Request $request, string $hashKey)
    {
        $response = new Response();
        $response->headers->set('X-Robots-Tag', 'noindex');

        try {
            $user = $this->userService->loadUserByToken($hashKey);
        } catch (NotFoundException $e) {
            $view = new InvalidLinkView(null);
            $view->setResponse($response);

            return $view;
        }
        $userPasswordResetData = new UserPasswordResetData(null, $user->getContentType());
        $form = $this->formFactory->resetUserPassword($userPasswordResetData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $currentUser = $this->permissionResolver->getCurrentUserReference();
                $this->permissionResolver->setCurrentUserReference($user);
            } catch (NotFoundException $e) {
                $view = new InvalidLinkView(null);
                $view->setResponse($response);

                return $view;
            }

            $data = $form->getData();

            try {
                $userUpdateStruct = $this->userService->newUserUpdateStruct();
                $userUpdateStruct->password = $data->getNewPassword();
                $this->userService->updateUser($user, $userUpdateStruct);
                $this->userService->expireUserToken($hashKey);
                $this->permissionResolver->setCurrentUserReference($currentUser);

                $view = new UserResetPasswordSuccessView(null);
                $view->setResponse($response);

                return $view;
            } catch (\Exception $e) {
                $this->notificationHandler->error(/** @Ignore */ $e->getMessage());
            }
        }

        $view = new UserResetPasswordFormView(null, [
            'form_reset_user_password' => $form->createView(),
        ]);
        $view->setResponse($response);

        return $view;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\User\User $user
     *
     * @return string
     *
     * @throws \Exception
     */
    private function updateUserToken(User $user): string
    {
        $struct = new UserTokenUpdateStruct();
        $struct->hashKey = bin2hex(random_bytes(16));
        $date = new DateTime();
        $date->add(new DateInterval($this->tokenIntervalSpec));
        $struct->time = $date;
        $this->userService->updateUserToken($user, $struct);

        return $struct->hashKey;
    }

    private function sendResetPasswordMessage(string $to, string $hashKey): void
    {
        $template = $this->twig->load($this->forgotPasswordMail);

        $subject = $template->renderBlock('subject', []);
        $from = $template->renderBlock('from', []);
        $body = $template->renderBlock('body', ['hash_key' => $hashKey]);

        $message = (new Swift_Message())
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($to)
            ->setBody($body, 'text/html');

        $this->mailer->send($message);
    }
}
