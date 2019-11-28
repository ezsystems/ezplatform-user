<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUser\Form\Type;

use EzSystems\EzPlatformUser\Form\Data\UserPasswordResetData;
use EzSystems\EzPlatformContentForms\Validator\Constraints\Password;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserPasswordResetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('new_password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => /** @Desc("Passwords do not match.") */ 'ezplatform.reset_user_password.passwords_must_match',
                'required' => true,
                'first_options' => ['label' => /** @Desc("New password") */ 'ezplatform.reset_user_password.new_password'],
                'second_options' => ['label' => /** @Desc("Confirm password") */ 'ezplatform.reset_user_password.confirm_new_password'],
                'constraints' => [
                    new Password(['contentType' => $options['content_type']]),
                ],
            ])
            ->add(
                'update',
                SubmitType::class,
                ['label' => /** @Desc("Update") */ 'ezplatform.reset_user_password.update']
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserPasswordResetData::class,
            'translation_domain' => 'forms',
            'content_type' => null,
        ]);
    }
}
