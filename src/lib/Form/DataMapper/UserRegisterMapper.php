<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformUser\Form\DataMapper;

use eZ\Publish\API\Repository\Values\Content\Field;
use EzSystems\EzPlatformUser\ConfigResolver\RegistrationContentTypeLoader;
use EzSystems\EzPlatformUser\ConfigResolver\RegistrationGroupLoader;
use EzSystems\RepositoryForms\Data\Content\FieldData;
use EzSystems\RepositoryForms\Data\User\UserRegisterData;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form data mapper for user creation.
 */
class UserRegisterMapper
{
    /** @var \EzSystems\EzPlatformUser\ConfigResolver\RegistrationContentTypeLoader */
    private $contentTypeLoader;

    /** @var \EzSystems\EzPlatformUser\ConfigResolver\RegistrationContentTypeLoader */
    private $parentGroupLoader;

    /** @var array */
    private $params;

    /**
     * @param \EzSystems\EzPlatformUser\ConfigResolver\RegistrationContentTypeLoader $contentTypeLoader
     * @param \EzSystems\EzPlatformUser\ConfigResolver\RegistrationGroupLoader $registrationGroupLoader
     */
    public function __construct(
        RegistrationContentTypeLoader $contentTypeLoader,
        RegistrationGroupLoader $registrationGroupLoader
    ) {
        $this->contentTypeLoader = $contentTypeLoader;
        $this->parentGroupLoader = $registrationGroupLoader;
    }

    /**
     * @param $name
     * @param $value
     */
    public function setParam($name, $value)
    {
        $this->params[$name] = $value;
    }

    /**
     * @return UserRegisterData
     */
    public function mapToFormData()
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->params = $resolver->resolve($this->params);

        $contentType = $this->contentTypeLoader->loadContentType();

        $data = new UserRegisterData([
            'contentType' => $contentType,
            'mainLanguageCode' => $this->params['language'],
            'enabled' => true,
        ]);
        $data->addParentGroup($this->parentGroupLoader->loadGroup());

        foreach ($contentType->fieldDefinitions as $fieldDef) {
            $data->addFieldData(new FieldData([
                'fieldDefinition' => $fieldDef,
                'field' => new Field([
                    'fieldDefIdentifier' => $fieldDef->identifier,
                    'languageCode' => $this->params['language'],
                ]),
                'value' => $fieldDef->defaultValue,
            ]));
        }

        return $data;
    }

    private function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setRequired('language');
    }
}
