<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUser\Form\ChoiceList\Loader;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Loader\BaseChoiceLoader;
use Symfony\Component\Intl\Locales;
use Symfony\Component\Validator\Constraints\Locale;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AvailableLocaleChoiceLoader extends BaseChoiceLoader
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

    /**
     * {@inheritdoc}
     */
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
}
