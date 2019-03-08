<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUser\View\UserSettings;

use eZ\Publish\Core\MVC\Symfony\View\BaseView;
use EzSystems\EzPlatformUser\UserSetting\UserSetting;

class UpdateView extends BaseView
{
    /** @var \EzSystems\EzPlatformUser\UserSetting\UserSetting|null */
    private $userSetting;

    /**
     * @return \EzSystems\EzPlatformUser\UserSetting\UserSetting
     */
    public function getUserSetting(): ?UserSetting
    {
        return $this->userSetting;
    }

    /**
     * @param \EzSystems\EzPlatformUser\UserSetting\UserSetting $userSetting|null
     */
    public function setUserSetting(?UserSetting $userSetting): void
    {
        $this->userSetting = $userSetting;
    }

    /**
     * {@inheritdoc}
     */
    protected function getInternalParameters(): array
    {
        return [
            'user_setting' => $this->getUserSetting(),
        ];
    }
}
