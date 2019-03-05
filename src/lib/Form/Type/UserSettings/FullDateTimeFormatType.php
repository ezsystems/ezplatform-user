<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUser\Form\Type\UserSettings;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FullDateTimeFormatType extends AbstractType
{
    /** @var string[] */
    private $allowedDateFormats;

    /** @var string[] */
    private $allowedTimeFormats;

    /**
     * @param string[] $allowedDateFormats
     * @param string[] $allowedTimeFormats
     */
    public function __construct(array $allowedDateFormats, array $allowedTimeFormats)
    {
        $this->allowedDateFormats = $allowedDateFormats;
        $this->allowedTimeFormats = $allowedTimeFormats;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'date_format_choices' => $this->allowedDateFormats,
            'time_format_choices' => $this->allowedTimeFormats,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return DateTimeFormatType::class;
    }
}
