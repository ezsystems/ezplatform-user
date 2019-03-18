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
use EzSystems\EzPlatformUser\UserSetting\DateTimeFormat\FormatterInterface;
use EzSystems\EzPlatformUser\UserSetting\UserSettingService;
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
     * @param \EzSystems\EzPlatformUser\UserSetting\DateTimeFormat\FormatterInterface $shortDateTimeFormatter
     * @param \EzSystems\EzPlatformUser\UserSetting\DateTimeFormat\FormatterInterface $fullDateTimeFormatter
     */
    public function __construct(
        UserSettingService $userSettingService,
        DateTimeFormatSerializer $dateTimeFormatSerializer,
        FormatterInterface $shortDateTimeFormatter,
        FormatterInterface $fullDateTimeFormatter
    ) {
        $this->userSettingService = $userSettingService;
        $this->dateTimeFormatSerializer = $dateTimeFormatSerializer;
        $this->shortDateTimeFormatter = $shortDateTimeFormatter;
        $this->fullDateTimeFormatter = $fullDateTimeFormatter;
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
            $date = new DateTimeImmutableDateTimeImmutable();
        }

        return $this->fullDateTimeFormatter->format($date);
    }
}
