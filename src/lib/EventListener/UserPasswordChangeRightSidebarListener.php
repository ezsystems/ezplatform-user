<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUser\EventListener;

use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use JMS\TranslationBundle\Model\Message;

class UserPasswordChangeRightSidebarListener implements EventSubscriberInterface, TranslationContainerInterface
{
    /* Menu items */
    public const ITEM__UPDATE = 'user_password_change__sidebar_right__update';
    public const ITEM__CANCEL = 'user_password_change__sidebar_right__cancel';

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [ConfigureMenuEvent::USER_PASSWORD_CHANGE_SIDEBAR_RIGHT => 'onUserPasswordChangeRightSidebarConfigure'];
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent $event
     */
    public function onUserPasswordChangeRightSidebarConfigure(ConfigureMenuEvent $event): void
    {
        $menu = $event->getMenu();

        $menu->addChild(
            self::ITEM__UPDATE,
            [
                'attributes' => [
                    'class' => 'btn--trigger',
                    'data-click' => '#user_password_change_change',
                ],
                'extras' => ['icon' => 'publish', 'translation_domain' => 'menu'],
            ]
        );
        $menu->addChild(
            self::ITEM__CANCEL,
            [
                'route' => 'ezplatform.dashboard',
                'extras' => ['icon' => 'circle-close', 'translation_domain' => 'menu'],
            ]
        );
    }

    /**
     * @return \JMS\TranslationBundle\Model\Message[]
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM__UPDATE, 'menu'))->setDesc('Update'),
            (new Message(self::ITEM__CANCEL, 'menu'))->setDesc('Discard changes'),
        ];
    }
}
