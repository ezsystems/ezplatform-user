<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUser\Form\Type;

use EzSystems\EzPlatformUser\Form\Data\UserPasswordForgotData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserPasswordForgotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'required' => true,
                'label' => /** @Desc("Enter your email") */ 'ezplatform.forgot_user_password.email',
            ])
            ->add(
                'reset',
                SubmitType::class,
                ['label' => /** @Desc("Send") */ 'ezplatform.forgot_user_password.send']
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserPasswordForgotData::class,
            'translation_domain' => 'forms',
        ]);
    }
}
