<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUser\UserSetting\Tools\DateTimeFormat;

use DateTimeInterface;
use IntlDateFormatter;

class Formatter implements FormatterInterface
{
    /** @var \IntlDateFormatter  */
    private $formatter;

    /**
     * @param string $locale
     * @param string $timezone
     * @param string $format
     */
    public function __construct(string $locale, string $timezone, string $format)
    {
        $this->formatter = new IntlDateFormatter(
            $locale,
            IntlDateFormatter::LONG,
            IntlDateFormatter::LONG,
            $timezone,
            null,
            $format
        );
    }

    /**
     * {@inheritdoc}
     */
    public function format(DateTimeInterface $datetime): string
    {
        return $this->formatter->format($datetime);
    }

}