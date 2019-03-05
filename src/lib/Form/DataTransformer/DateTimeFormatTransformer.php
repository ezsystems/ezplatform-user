<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUser\Form\DataTransformer;

use EzSystems\EzPlatformUser\UserSetting\Setting\DateTimeFormatSerializer;
use EzSystems\EzPlatformUser\UserSetting\Setting\Value\DateTimeFormat;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class DateTimeFormatTransformer implements DataTransformerInterface
{
    /** @var \EzSystems\EzPlatformUser\UserSetting\Setting\DateTimeFormatSerializer */
    private $serializer;

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
    public function transform($value): ?array
    {
        if (null === $value) {
            return null;
        }

        if (!is_string($value)) {
            throw new TransformationFailedException(
                sprintf('Expected a %s, got %s instead', 'string', gettype($value))
            );
        }

        $value = $this->serializer->deserialize($value);

        return [
            'date_format' => $value->getDateFormat(),
            'time_format' => $value->getTimeFormat(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if (!is_array($value)) {
            throw new TransformationFailedException(
                sprintf('Expected a array, got %s instead', gettype($value))
            );
        }

        return $this->serializer->serialize(new DateTimeFormat(
            $value['date_format'], $value['time_format']
        ));
    }
}
