<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUser\Form\Factory;

use EzSystems\EzPlatformUser\Form\Data\User\UserPasswordForgotData;
use EzSystems\EzPlatformUser\Form\Data\User\UserPasswordChangeData;
use EzSystems\EzPlatformUser\Form\Type\User\UserPasswordChangeType;
use EzSystems\EzPlatformUser\Form\Type\User\UserPasswordForgotType;
use EzSystems\EzPlatformUser\Form\Data\User\UserPasswordForgotWithLoginData;
use EzSystems\EzPlatformUser\Form\Type\User\UserPasswordForgotWithLoginType;
use EzSystems\EzPlatformUser\Form\Data\User\UserPasswordResetData;
use EzSystems\EzPlatformUser\Form\Type\User\UserPasswordResetType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Util\StringUtil;

class FormFactory
{
    /** @var \Symfony\Component\Form\FormFactoryInterface */
    protected $formFactory;

    /** @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface  */
    protected $urlGenerator;

    /**
     * @param \Symfony\Component\Form\FormFactoryInterface $formFactory
     * @param \Symfony\Component\Routing\Generator\UrlGeneratorInterface $urlGenerator
     */
    public function __construct(FormFactoryInterface $formFactory, UrlGeneratorInterface $urlGenerator)
    {
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param \EzSystems\EzPlatformUser\Form\Data\User\UserPasswordChangeData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function changeUserPassword(
        UserPasswordChangeData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(UserPasswordChangeType::class);

        return $this->formFactory->createNamed($name, UserPasswordChangeType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformUser\Form\Data\User\UserPasswordForgotData $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function forgotUserPassword(
        UserPasswordForgotData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(UserPasswordForgotType::class);

        return $this->formFactory->createNamed($name, UserPasswordForgotType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformUser\Form\Data\User\UserPasswordForgotWithLoginData $data
     * @param null|string $name
     *
     * @return \Symfony\Component\Form\FormInterface
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function forgotUserPasswordWithLogin(
        UserPasswordForgotWithLoginData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(UserPasswordForgotWithLoginType::class);

        return $this->formFactory->createNamed($name, UserPasswordForgotWithLoginType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformUser\Form\Data\User\UserPasswordResetData $data
     * @param null|string $name
     *
     * @return \Symfony\Component\Form\FormInterface
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function resetUserPassword(
        UserPasswordResetData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(UserPasswordResetType::class);

        return $this->formFactory->createNamed($name, UserPasswordResetType::class, $data);
    }

}
