<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUserBundle\Controller;

use eZ\Publish\API\Repository\UserService;
use EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface;
use EzSystems\EzPlatformAdminUi\Specification\SiteAccess\IsAdmin;
use EzSystems\EzPlatformUser\Form\Factory\FormFactory;
use EzSystems\EzPlatformUser\View\ChangePassword\FormView;
use EzSystems\EzPlatformUser\View\ChangePassword\SuccessView;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Exception;

class PasswordChangeController extends Controller
{
    /** @var \EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface */
    private $notificationHandler;

    /** @var \eZ\Publish\API\Repository\UserService */
    private $userService;

    /** @var \EzSystems\EzPlatformUser\Form\Factory\FormFactory */
    private $formFactory;

    /** @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface */
    private $tokenStorage;

    /** @var array */
    private $siteAccessGroups;

    /**
     * @param \EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface $notificationHandler
     * @param \eZ\Publish\API\Repository\UserService $userService
     * @param \EzSystems\EzPlatformUser\Form\Factory\FormFactory $formFactory
     * @param \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface $tokenStorage
     * @param array $siteAccessGroups
     */
    public function __construct(
        TranslatableNotificationHandlerInterface $notificationHandler,
        UserService $userService,
        FormFactory $formFactory,
        TokenStorageInterface $tokenStorage,
        array $siteAccessGroups
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->userService = $userService;
        $this->formFactory = $formFactory;
        $this->tokenStorage = $tokenStorage;
        $this->siteAccessGroups = $siteAccessGroups;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \EzSystems\EzPlatformUser\View\ChangePassword\FormView|\EzSystems\EzPlatformUser\View\ChangePassword\SuccessView|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     */
    public function userPasswordChangeAction(Request $request)
    {
        /** @var \eZ\Publish\API\Repository\Values\User\User $user */
        $user = $this->tokenStorage->getToken()->getUser()->getAPIUser();
        $form = $this->formFactory->changeUserPassword($user->getContentType(), null, null, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $this->userService->updateUserPassword($user, $data->getNewPassword());

                if ((new IsAdmin($this->siteAccessGroups))->isSatisfiedBy($request->attributes->get('siteaccess'))) {
                    $this->notificationHandler->success(
                        /** @Desc("Your password has been successfully changed.") */
                        'ezplatform.change_password.success',
                        [],
                        'change_password'
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
