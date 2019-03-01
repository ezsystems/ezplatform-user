<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUser\UserSetting\Setting;

use EzSystems\EzPlatformUser\UserSetting\FormMapperInterface;
use EzSystems\EzPlatformUser\UserSetting\ValueDefinitionInterface;
use EzSystems\EzPlatformAdminUi\UserSetting as AdminUiUserSettings;
use InvalidArgumentException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

class CollapseContentPreview implements ValueDefinitionInterface, FormMapperInterface
{
    public const VALUE_COLLAPSED = 'true';
    public const VALUE_EXPANDED = 'false';

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
        switch ($storageValue) {
            case self::VALUE_COLLAPSED:
                return $this->translator->trans(
                    /** @Desc("Yes") */
                    'settings.collapse_content_preview.value.true',
                    [],
                    'user_settings'
                );
            case self::VALUE_EXPANDED:
                return $this->translator->trans(
                    /** @Desc("No") */
                    'settings.collapse_content_preview.value.false',
                    [],
                    'user_settings'
                );
            default:
                throw new InvalidArgumentException(sprintf('Invalid value of $storageValue: %s', $storageValue));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultValue(): string
    {
        return self::VALUE_EXPANDED;
    }

    /**
     * {@inheritdoc}
     */
    public function mapFieldForm(FormBuilderInterface $formBuilder, AdminUiUserSettings\ValueDefinitionInterface $value): FormBuilderInterface
    {
        return $formBuilder->create(
            'value',
            ChoiceType::class,
            [
                'required' => true,
                'label' => $this->getTranslatedDescription(),
                'choices' => [
                    $this->getDisplayValue(self::VALUE_COLLAPSED) => self::VALUE_COLLAPSED,
                    $this->getDisplayValue(self::VALUE_EXPANDED) => self::VALUE_EXPANDED,
                ],
            ]
        );
    }

    /**
     * @return string
     */
    private function getTranslatedName(): string
    {
        return $this->translator->trans(
            /** @Desc("Collapse content preview") */
            'settings.collapse_content_preview.value.title',
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
            /** @Desc("Collapse preview by default") */
            'settings.collapse_content_preview.value.description',
            [],
            'user_settings'
        );
    }
}
