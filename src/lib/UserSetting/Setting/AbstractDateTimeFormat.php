<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUser\UserSetting\Setting;

use EzSystems\EzPlatformUser\UserSetting\FormMapperInterface;
use EzSystems\EzPlatformUser\UserSetting\ValueDefinitionInterface;

abstract class AbstractDateTimeFormat implements ValueDefinitionInterface, FormMapperInterface
{
    /** @var \EzSystems\EzPlatformUser\UserSetting\Setting\DateTimeFormatSerializer */
    protected $serializer;

    /**
     * @param \EzSystems\EzPlatformUser\UserSetting\Setting\DateTimeFormatSerializer $serializer
     */
    public function __construct(DateTimeFormatSerializer $serializer)
    {
        $this->serializer = $serializer;
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

        return "$dateFormatLabel $timeFormatLabel";
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
