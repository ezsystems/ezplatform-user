<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUser\UserSetting\Setting;

use EzSystems\EzPlatformUser\UserSetting\FormMapperInterface;
use EzSystems\EzPlatformUser\UserSetting\ValueDefinitionInterface;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use EzSystems\EzPlatformAdminUi\UserSetting as AdminUiUserSettings;

class CharacterCounter implements ValueDefinitionInterface, FormMapperInterface
{
    public const ENABLED_OPTION = 'enabled';
    public const DISABLED_OPTION = 'disabled';

    /** @var \Symfony\Component\Translation\TranslatorInterface */
    private $translator;

    /**
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
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
        switch($storageValue) {
            case self::ENABLED_OPTION:
                return $this->getTranslatedOptionEnabled();
            case self::DISABLED_OPTION:
                return $this->getTranslatedOptionDisabled();
            default:
                throw new InvalidArgumentException(
                    '$storageValue',
                    sprintf('There is no \'%s\' option', $storageValue)
                );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultValue(): string
    {
        return 'enabled';
    }

    /**
     * {@inheritdoc}
     */
    public function mapFieldForm(FormBuilderInterface $formBuilder, AdminUiUserSettings\ValueDefinitionInterface $value): FormBuilderInterface
    {
        $choices = [
            $this->getTranslatedOptionEnabled() => self::ENABLED_OPTION,
            $this->getTranslatedOptionDisabled() => self::DISABLED_OPTION,
        ];

        return $formBuilder->create(
            'value',
            ChoiceType::class,
            [
                'multiple' => false,
                'required' => true,
                'label' => $this->getTranslatedDescription(),
                'choices' => $choices,
            ]
        );
    }

    /**
     * @return string
     */
    private function getTranslatedName(): string
    {
        return $this->translator->trans(
            /** @Desc("Character counter") */
            'settings.character_counter.value.title',
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
            /** @Desc("Displays count of characters typed in Online Editor") */
            'settings.character_counter.value.description',
            [],
            'user_settings'
        );
    }

    /**
     * @return string
     */
    private function getTranslatedOptionEnabled(): string
    {
        return $this->translator->trans(
            /** @Desc("enabled") */
            'settings.character_counter.value.enabled',
            [],
            'user_settings'
        );
    }

    /**
     * @return string
     */
    private function getTranslatedOptionDisabled(): string
    {
        return $this->translator->trans(
            /** @Desc("disabled") */
            'settings.character_counter.value.disabled',
            [],
            'user_settings'
        );
    }
}
