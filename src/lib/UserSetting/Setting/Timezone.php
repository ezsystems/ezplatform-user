<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUser\UserSetting\Setting;

use Ibexa\Contracts\User\UserSetting\FormMapperInterface;
use Ibexa\Contracts\User\UserSetting\ValueDefinitionInterface;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class Timezone implements ValueDefinitionInterface, FormMapperInterface
{
    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    /**
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
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
        return $storageValue;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultValue(): string
    {
        return date_default_timezone_get();
    }

    /**
     * {@inheritdoc}
     */
    public function mapFieldForm(FormBuilderInterface $formBuilder, ValueDefinitionInterface $value): FormBuilderInterface
    {
        return $formBuilder->create(
            'value',
            TimezoneType::class,
            [
                'multiple' => false,
                'required' => true,
                'label' => $this->getTranslatedDescription(),
            ]
        );
    }

    /**
     * @return string
     */
    private function getTranslatedName(): string
    {
        return $this->translator->trans(
            /** @Desc("Time Zone") */
            'settings.timezone.value.title',
            [],
            'user_settings'
        );
    }

    /**
     * @return string
     */
    private function getTranslatedDescription(): string
    {
        return $this->translator->trans(
            /** @Desc("User Time Zone") */
            'settings.timezone.value.description',
            [],
            'user_settings'
        );
    }
}
