<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUser\Templating\Twig;

use DateTimeImmutable;
use DateTimeInterface;
use EzSystems\EzPlatformUser\UserSetting\Setting\DateTimeFormatSerializer;
use EzSystems\EzPlatformUser\UserSetting\UserSettingService;
use IntlDateFormatter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class DateTimeExtension extends AbstractExtension
{
    /** @var \EzSystems\EzPlatformUser\UserSetting\UserSettingService */
    private $userSettingService;

    /** @var \EzSystems\EzPlatformUser\UserSetting\Setting\DateTimeFormatSerializer */
    private $dateTimeFormatSerializer;

    /** @var \IntlDateFormatter */
    private $shortDateTimeFormatter;

    /** @var \IntlDateFormatter */
    private $fullDateTimeFormatter;

    /**
     * @param \EzSystems\EzPlatformUser\UserSetting\UserSettingService $userSettingService
     * @param \EzSystems\EzPlatformUser\UserSetting\Setting\DateTimeFormatSerializer $dateTimeFormatSerializer
     */
    public function __construct(
        UserSettingService $userSettingService,
        DateTimeFormatSerializer $dateTimeFormatSerializer
    ) {
        $this->userSettingService = $userSettingService;
        $this->dateTimeFormatSerializer = $dateTimeFormatSerializer;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('ez_short_datetime', [$this, 'toShortFormat']),
            new TwigFilter('ez_full_datetime', [$this, 'toFullFormat']),
        ];
    }

    /**
     * @param DateTimeInterface|null $date
     *
     * @return string
     */
    public function toShortFormat(?DateTimeInterface $date = null): string
    {
        if ($date === null) {
            $date = new DateTimeImmutable();
        }

        return $this->getShortDateTimeFormatter()->format($date);
    }

    /**
     * @param DateTimeInterface|null $date
     *
     * @return string
     */
    public function toFullFormat(?DateTimeInterface $date = null): string
    {
        if ($date === null) {
            $date = new DateTimeImmutable();
        }

        return $this->getFullDateTimeFormatter()->format($date);
    }

    /**
     * @return \IntlDateFormatter
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    private function getShortDateTimeFormatter(): IntlDateFormatter
    {
        if ($this->shortDateTimeFormatter === null) {
            $langauge = $this->userSettingService->getUserSetting('language')->value;
            $timezone = $this->userSettingService->getUserSetting('timezone')->value;

            $shortDateFormat = (string)$this->dateTimeFormatSerializer->deserialize(
                $this->userSettingService->getUserSetting('short_datetime_format')->value
            );

            $this->shortDateTimeFormatter = new IntlDateFormatter(
                $langauge,
                IntlDateFormatter::LONG,
                IntlDateFormatter::LONG,
                $timezone,
                null,
                $shortDateFormat
            );
        }

        return $this->shortDateTimeFormatter;
    }

    /**
     * @return \IntlDateFormatter
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    private function getFullDateTimeFormatter(): IntlDateFormatter
    {
        if ($this->fullDateTimeFormatter === null) {
            $langauge = $this->userSettingService->getUserSetting('language')->value;
            $timezone = $this->userSettingService->getUserSetting('timezone')->value;

            $fullDateFormat = (string)$this->dateTimeFormatSerializer->deserialize(
                $this->userSettingService->getUserSetting('full_datetime_format')->value
            );

            $this->fullDateTimeFormatter = new IntlDateFormatter(
                $langauge,
                IntlDateFormatter::LONG,
                IntlDateFormatter::LONG,
                $timezone,
                null,
                $fullDateFormat
            );
        }

        return $this->fullDateTimeFormatter;
    }
}
