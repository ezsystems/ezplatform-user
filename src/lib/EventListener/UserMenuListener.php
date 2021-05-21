<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUser\EventListener;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\UserService;
use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserMenuListener implements EventSubscriberInterface, TranslationContainerInterface
{
    public const ITEM_CHANGE_PASSWORD = 'user__change_password';

    /** @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface */
    private $tokenStorage;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \eZ\Publish\API\Repository\UserService */
    private $userService;

    /**
     * @param \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface $tokenStorage
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     * @param \eZ\Publish\API\Repository\UserService $userService
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        PermissionResolver $permissionResolver,
        UserService $userService
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->permissionResolver = $permissionResolver;
        $this->userService = $userService;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [ConfigureMenuEvent::USER_MENU => 'onUserMenuConfigure'];
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent $event
     */
    public function onUserMenuConfigure(ConfigureMenuEvent $event): void
    {
        $menu = $event->getMenu();
        $token = $this->tokenStorage->getToken();

        $currentUserId = $this->permissionResolver->getCurrentUserReference()->getUserId();
        try {
            $currentUser = $this->userService->loadUser($currentUserId);
        } catch (NotFoundException $e) {
            return;
        }

        if (null !== $token &&
            is_object($token->getUser()) &&
            $this->permissionResolver->canUser('user', 'password', $currentUser, [$currentUser])
        ) {
            $menu->addChild(
                self::ITEM_CHANGE_PASSWORD,
                [
                    'route' => 'ezplatform.user_profile.change_password',
                    'extras' => [
                        'translation_domain' => 'menu',
                        'orderNumber' => 10,
                    ],
                ]
            );
        }
    }

    /**
     * @return \JMS\TranslationBundle\Model\Message[]
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM_CHANGE_PASSWORD, 'menu'))->setDesc('Change password'),
        ];
    }
}
