<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUser\Form\Type;

use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use EzSystems\EzPlatformUser\Form\Data\UserPasswordChangeData;
use EzSystems\EzPlatformUser\Validator\Constraints\Password;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserPasswordChangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, [
                'required' => true,
                'label' => /** @Desc("Current password") */ 'ezplatform.change_user_password.old_password',
            ])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => /** @Desc("Passwords do not match.") */ 'ezplatform.change_user_password.passwords_must_match',
                'required' => true,
                'first_options' => ['label' => /** @Desc("New password") */ 'ezplatform.change_user_password.new_password'],
                'second_options' => ['label' => /** @Desc("Confirm password") */ 'ezplatform.change_user_password.confirm_new_password'],
                'constraints' => [
                    new Password([
                        'contentType' => $options['content_type'],
                    ]),
                ],
            ])
            ->add(
                'change',
                SubmitType::class,
                ['label' => /** @Desc("Change") */ 'ezplatform.change_user_password.change']
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('content_type');
        $resolver->setAllowedTypes('content_type', ContentType::class);
        $resolver->setDefaults([
            'data_class' => UserPasswordChangeData::class,
            'translation_domain' => 'forms',
        ]);
    }
}
