<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUser\UserSetting;

use Symfony\Component\Form\FormBuilderInterface;

interface FormMapperInterface
{
    /**
     * Creates 'value' form type representing editing control for setting user preference value.
     */
    public function mapFieldForm(
        FormBuilderInterface $formBuilder,
        ValueDefinitionInterface $value
    ): FormBuilderInterface;
}
