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

final class DateTimeExtension extends AbstractExtension
{
    /** @var \IntlDateFormatter */
    private $shortDateTimeFormatter;

    /** @var \IntlDateFormatter */
    private $fullDateTimeFormatter;

    /**
     * @param UserSettingService $userSettingService
     * @param DateTimeFormatSerializer $dateTimeFormatSerializer
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function __construct(
        UserSettingService $userSettingService,
        DateTimeFormatSerializer $dateTimeFormatSerializer
    ) {
        $langauge = $userSettingService->getUserSetting('language')->value;
        $timezone = $userSettingService->getUserSetting('timezone')->value;

        $shortDateFormat = (string)$dateTimeFormatSerializer->deserialize(
            $userSettingService->getUserSetting('short_datetime_format')->value
        );

        $fullDateFormat = (string)$dateTimeFormatSerializer->deserialize(
            $userSettingService->getUserSetting('full_datetime_format')->value
        );

        $this->shortDateTimeFormatter = new IntlDateFormatter(
            $langauge,
            IntlDateFormatter::LONG,
            IntlDateFormatter::LONG,
            $timezone,
            null,
            $shortDateFormat
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

        return $this->shortDateTimeFormatter->format($date);
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

        return $this->fullDateTimeFormatter->format($date);
    }
}
