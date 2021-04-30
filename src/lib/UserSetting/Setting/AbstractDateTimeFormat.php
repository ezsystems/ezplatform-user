<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUser\UserSetting\Setting;

use EzSystems\EzPlatformUser\UserSetting\FormMapperInterface;
use EzSystems\EzPlatformUser\UserSetting\DateTimeFormat\FormatterInterface;
use EzSystems\EzPlatformUser\UserSetting\ValueDefinitionInterface;
use DateTimeImmutable;

abstract class AbstractDateTimeFormat implements ValueDefinitionInterface, FormMapperInterface
{
    /** @var \EzSystems\EzPlatformUser\UserSetting\Setting\DateTimeFormatSerializer */
    protected $serializer;

    /** @var \EzSystems\EzPlatformUser\UserSetting\DateTimeFormat\Formatter|null */
    protected $formatter;

    /**
     * @param \EzSystems\EzPlatformUser\UserSetting\Setting\DateTimeFormatSerializer $serializer
     * @param \EzSystems\EzPlatformUser\UserSetting\DateTimeFormat\FormatterInterface $formatter
     */
    public function __construct(DateTimeFormatSerializer $serializer, FormatterInterface $formatter)
    {
        $this->serializer = $serializer;
        $this->formatter = $formatter;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->getTranslatedName();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(): string
    {
        return $this->getTranslatedDescription();
    }

    /**
     * {@inheritdoc}
     */
    public function getDisplayValue(string $storageValue): string
    {
        $dateTimeFormat = $this->serializer->deserialize($storageValue);

        $allowedDateFormats = array_flip($this->getAllowedDateFormats());
        $allowedTimeFormats = array_flip($this->getAllowedTimeFormats());

        $dateFormatLabel = $dateTimeFormat->getDateFormat();
        if (isset($allowedDateFormats[$dateFormatLabel])) {
            $dateFormatLabel = $allowedDateFormats[$dateFormatLabel];
        }

        $timeFormatLabel = $dateTimeFormat->getTimeFormat();
        if (isset($allowedTimeFormats[$timeFormatLabel])) {
            $timeFormatLabel = $allowedTimeFormats[$timeFormatLabel];
        }

        $demoValue = $this->formatter->format(new DateTimeImmutable());

        return "$demoValue ($dateFormatLabel $timeFormatLabel)";
    }

    /**
     * @return string[]
     */
    abstract protected function getAllowedTimeFormats(): array;

    /**
     * @return string[]
     */
    abstract protected function getAllowedDateFormats(): array;

    /**
     * @return string
     */
    abstract protected function getTranslatedName(): string;

    /**
     * @return string
     */
    abstract protected function getTranslatedDescription(): string;
}
