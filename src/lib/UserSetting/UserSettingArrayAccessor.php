<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUser\UserSetting;

use ArrayAccess;
use eZ\Publish\API\Repository\Exceptions\NotImplementedException;

/**
 * @internal
 */
class UserSettingArrayAccessor implements ArrayAccess
{
    /** @var \EzSystems\EzPlatformUser\UserSetting\UserSettingService */
    protected $userSettingService;

    /**
     * @param \EzSystems\EzPlatformUser\UserSetting\UserSettingService $userSettingService
     */
    public function __construct(UserSettingService $userSettingService)
    {
        $this->userSettingService = $userSettingService;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset): bool
    {
        // @todo refactor once UserSettingService supports this natively

        return null !== $this->userSettingService->getUserSetting($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset): string
    {
        return $this->userSettingService->getUserSetting($offset)->value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value): void
    {
        $this->userSettingService->setUserSetting($offset, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset): void
    {
        // UserSettingService doesn't provide this feature

        throw new NotImplementedException('offsetUnset');
    }
}
