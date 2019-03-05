<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUser\Form\Type\UserSettings;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateTimeFormatType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('date_format', ChoiceType::class, [
            'label' => /** @Desc("Format for displaying Date") */ 'ezplatform.date_time_format.date_format.label',
            'choices' => $options['date_format_choices'],
            'multiple' => false,
            'required' => true,
        ]);

        $builder->add('time_format', ChoiceType::class, [
            'label' => /** @Desc("Format for displaying Date") */ 'ezplatform.date_time_format.time_format.label',
            'choices' => $options['time_format_choices'],
            'multiple' => false,
            'required' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'date_format_choices' => [],
            'time_format_choices' => [],
            'translation_domain' => 'user_settings'
        ]);
    }
}
