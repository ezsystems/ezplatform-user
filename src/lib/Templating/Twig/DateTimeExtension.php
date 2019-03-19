<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUser\Templating\Twig;

use DateTimeImmutable;
use EzSystems\EzPlatformUser\UserSetting\Setting\DateTimeFormatSerializer;
use EzSystems\EzPlatformUser\UserSetting\DateTimeFormat\FormatterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class DateTimeExtension extends AbstractExtension
{
    /** @var \EzSystems\EzPlatformUser\UserSetting\Setting\DateTimeFormatSerializer */
    private $dateTimeFormatSerializer;

    /** @var \EzSystems\EzPlatformUser\UserSetting\DateTimeFormat\FormatterInterface */
    private $shortDateTimeFormatter;

    /** @var \EzSystems\EzPlatformUser\UserSetting\DateTimeFormat\FormatterInterface */
    private $shortDateFormatter;

    /** @var \EzSystems\EzPlatformUser\UserSetting\DateTimeFormat\FormatterInterface */
    private $shortTimeFormatter;

    /** @var \EzSystems\EzPlatformUser\UserSetting\DateTimeFormat\FormatterInterface */
    private $fullDateTimeFormatter;

    /** @var \EzSystems\EzPlatformUser\UserSetting\DateTimeFormat\FormatterInterface */
    private $fullDateFormatter;

    /** @var \EzSystems\EzPlatformUser\UserSetting\DateTimeFormat\FormatterInterface */
    private $fullTimeFormatter;

    /**
     * @param \EzSystems\EzPlatformUser\UserSetting\Setting\DateTimeFormatSerializer $dateTimeFormatSerializer
     * @param \EzSystems\EzPlatformUser\UserSetting\DateTimeFormat\FormatterInterface $shortDateTimeFormatter
     * @param \EzSystems\EzPlatformUser\UserSetting\DateTimeFormat\FormatterInterface $shortDateFormatter
     * @param \EzSystems\EzPlatformUser\UserSetting\DateTimeFormat\FormatterInterface $shortTimeFormatter
     * @param \EzSystems\EzPlatformUser\UserSetting\DateTimeFormat\FormatterInterface $fullDateTimeFormatter
     * @param \EzSystems\EzPlatformUser\UserSetting\DateTimeFormat\FormatterInterface $fullDateFormatter
     * @param \EzSystems\EzPlatformUser\UserSetting\DateTimeFormat\FormatterInterface $fullTimeFormatter
     */
    public function __construct(
        DateTimeFormatSerializer $dateTimeFormatSerializer,
        FormatterInterface $shortDateTimeFormatter,
        FormatterInterface $shortDateFormatter,
        FormatterInterface $shortTimeFormatter,
        FormatterInterface $fullDateTimeFormatter,
        FormatterInterface $fullDateFormatter,
        FormatterInterface $fullTimeFormatter
    ) {
        $this->dateTimeFormatSerializer = $dateTimeFormatSerializer;
        $this->shortDateTimeFormatter = $shortDateTimeFormatter;
        $this->shortDateFormatter = $shortDateFormatter;
        $this->shortTimeFormatter = $shortTimeFormatter;
        $this->fullDateTimeFormatter = $fullDateTimeFormatter;
        $this->fullDateFormatter = $fullDateFormatter;
        $this->fullTimeFormatter = $fullTimeFormatter;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('ez_short_datetime', function ($date) { return $this->format($date, $this->shortDateTimeFormatter); }),
            new TwigFilter('ez_short_date', function ($date) { return $this->format($date, $this->shortDateFormatter); }),
            new TwigFilter('ez_short_time', function ($date) { return $this->format($date, $this->shortTimeFormatter); }),
            new TwigFilter('ez_full_datetime', function ($date) { return $this->format($date, $this->fullDateTimeFormatter); }),
            new TwigFilter('ez_full_date', function ($date) { return $this->format($date, $this->fullDateFormatter); }),
            new TwigFilter('ez_full_time', function ($date) { return $this->format($date, $this->fullTimeFormatter); }),
        ];
    }

    /**
     * @param mixed $date
     * @param \EzSystems\EzPlatformUser\UserSetting\DateTimeFormat\FormatterInterface  $formatter
     *
     * @return string
     */
    public function format($date = null, FormatterInterface $formatter): string
    {
        if ($date === null) {
            $date = new DateTimeImmutable();
        }

        if (is_int($date)) {
            $date = new DateTimeImmutable('@'.$date);
        }

        return $formatter->format($date);
    }
}
