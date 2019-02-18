<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUserBundle\Controller;

use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\LanguageService;
use EzSystems\EzPlatformAdminUi\Specification\SiteAccess\IsAdmin;
use EzSystems\EzPlatformUser\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use EzSystems\EzPlatformUser\View\ForgotPassword\ChangePassword\FormView;
use EzSystems\EzPlatformUser\View\ForgotPassword\ChangePassword\SuccessView;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Exception;

class PasswordChangeController extends Controller
{
    /** @var NotificationHandlerInterface */
    private $notificationHandler;

    /** @var TranslatorInterface */
    private $translator;

    /** @var LanguageService */
    private $userService;

    /** @var FormFactory */
    private $formFactory;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var array */
    private $siteAccessGroups;

    /**
     * @param NotificationHandlerInterface $notificationHandler
     * @param TranslatorInterface $translator
     * @param UserService $userService
     * @param FormFactory $formFactory
     * @param TokenStorageInterface $tokenStorage
     * @param array $siteAccessGroups
     */
    public function __construct(
        NotificationHandlerInterface $notificationHandler,
        TranslatorInterface $translator,
        UserService $userService,
        FormFactory $formFactory,
        TokenStorageInterface $tokenStorage,
        array $siteAccessGroups
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->translator = $translator;
        $this->userService = $userService;
        $this->formFactory = $formFactory;
        $this->tokenStorage = $tokenStorage;
        $this->siteAccessGroups = $siteAccessGroups;
    }

    public function performAccessCheck()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \EzSystems\EzPlatformUser\View\ForgotPassword\ChangePassword\FormView|\EzSystems\EzPlatformUser\View\ForgotPassword\ChangePassword\SuccessView|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     */
    public function userPasswordChangeAction(Request $request)
    {
        $form = $this->formFactory->changeUserPassword();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $newPassword = $data->getNewPassword();
                $userUpdateStruct = $this->userService->newUserUpdateStruct();
                $userUpdateStruct->password = $newPassword;
                $user = $this->tokenStorage->getToken()->getUser()->getAPIUser();
                $this->userService->updateUser($user, $userUpdateStruct);

                if ((new IsAdmin($this->siteAccessGroups))->isSatisfiedBy($request->attributes->get('siteaccess'))) {
                    $this->notificationHandler->success(
                        $this->translator->trans(
                            /** @Desc("Your password has been successfully changed.") */
                            'ezplatform.change_password.success',
                            [],
                            'change_password'
                        )
                    );

                    return new RedirectResponse($this->generateUrl('ezplatform.dashboard'));
                }

                return new SuccessView(null);
            } catch (Exception $e) {
                $this->notificationHandler->error($e->getMessage());
            }
        }

        return new FormView(null, [
            'form_change_user_password' => $form->createView(),
        ]);
    }
}
