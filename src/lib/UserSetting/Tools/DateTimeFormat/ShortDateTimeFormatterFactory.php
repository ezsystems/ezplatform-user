<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUser\UserSetting\Tools\DateTimeFormat;

use EzSystems\EzPlatformUser\UserSetting\Setting\DateTimeFormatSerializer;
use EzSystems\EzPlatformUser\UserSetting\UserSettingService;

class ShortDateTimeFormatterFactory implements DateTimeFormatterFactoryInterface
{
    /** @var \EzSystems\EzPlatformUser\UserSetting\UserSettingService */
    private $userSettingService;

    /** @var \EzSystems\EzPlatformUser\UserSetting\Setting\DateTimeFormatSerializer  */
    private $dateTimeFormatSerializer;

    /**
     * @param \EzSystems\EzPlatformUser\UserSetting\UserSettingService $userSettingService
     * @param \EzSystems\EzPlatformUser\UserSetting\Setting\DateTimeFormatSerializer $dateTimeFormatSerializer
     */
    public function __construct(UserSettingService $userSettingService, DateTimeFormatSerializer $dateTimeFormatSerializer)
    {
        $this->userSettingService = $userSettingService;
        $this->dateTimeFormatSerializer = $dateTimeFormatSerializer;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormatter(): Formatter
    {
        $language = $this->userSettingService->getUserSetting('language')->value;
        $timezone = $this->userSettingService->getUserSetting('timezone')->value;

        $shortDateFormat = (string)$this->dateTimeFormatSerializer->deserialize(
            $this->userSettingService->getUserSetting('short_datetime_format')->value
        );

        return new Formatter(
            $language,
            $timezone,
            $shortDateFormat
        );
    }
}