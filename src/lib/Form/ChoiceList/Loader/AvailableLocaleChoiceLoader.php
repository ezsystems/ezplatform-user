<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUser\Form\ChoiceList\Loader;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;
use Symfony\Component\Intl\Locales;
use Symfony\Component\Validator\Constraints\Locale;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AvailableLocaleChoiceLoader implements ChoiceLoaderInterface
{
    /** @var \Symfony\Component\Validator\Validator\ValidatorInterface */
    private $validator;

    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    /** @var string[] */
    private $availableTranslations;

    /**
     * @param \Symfony\Component\Validator\Validator\ValidatorInterface $validator
     * @param \eZ\Publish\Core\MVC\ConfigResolverInterface $configResolver
     * @param string[] $availableTranslations
     */
    public function __construct(
        ValidatorInterface $validator,
        ConfigResolverInterface $configResolver,
        array $availableTranslations
    ) {
        $this->validator = $validator;
        $this->availableTranslations = $availableTranslations;
        $this->configResolver = $configResolver;
    }

    public function getChoiceList(): array
    {
        $choices = [];

        $additionalTranslations = $this->configResolver->getParameter('user_preferences.additional_translations');
        $availableLocales = array_unique(array_merge($this->availableTranslations, $additionalTranslations));

        foreach ($availableLocales as $locale) {
            if (0 === $this->validator->validate($locale, new Locale())->count()) {
                $choices[Locales::getName($locale)] = $locale;
            }
        }

        return $choices;
    }

    public function loadChoiceList($value = null)
    {
        return new ArrayChoiceList($this->getChoiceList(), $value);
    }

    public function loadChoicesForValues(array $values, $value = null)
    {
        // Optimize
        $values = array_filter($values);
        if (empty($values)) {
            return [];
        }

        return $this->loadChoiceList($value)->getChoicesForValues($values);
    }

    public function loadValuesForChoices(array $choices, $value = null)
    {
        // Optimize
        $choices = array_filter($choices);
        if (empty($choices)) {
            return [];
        }

        // If no callable is set, choices are the same as values
        if (null === $value) {
            return $choices;
        }

        return $this->loadChoiceList($value)->getValuesForChoices($choices);
    }
}
