<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use EzSystems\EzPlatformUser\EventListener\UserMenuListener;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
final class UserMenuListenerTest extends TestCase
{

    public function testOnUserMenuConfigureAnonUser(): void
    {
        $factory = $this->getMockBuilder(FactoryInterface::class)->getMock();
        $menu = $this->getMockBuilder(ItemInterface::class)->getMock();
        $menu->expects($this->never())->method('reorderChildren');
        $event = new ConfigureMenuEvent($factory, $menu);
        $tokenStorage = $this->getMockBuilder(TokenStorageInterface::class)->disableOriginalConstructor()->getMock();
        $listener = new UserMenuListener($tokenStorage);
        $listener->onUserMenuConfigure($event);
    }

}
