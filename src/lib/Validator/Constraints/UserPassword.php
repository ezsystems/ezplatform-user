<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUser\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

/**
 * @Annotation
 */
class UserPassword extends Constraint implements TranslationContainerInterface
{
    public $message = 'ezplatform.change_user_password.not_match';

    /**
     * @return \JMS\TranslationBundle\Model\Message[]
     */
    public static function getTranslationMessages(): array
    {
        return [
            Message::create('ezplatform.change_user_password.not_match', 'validators')
                ->setDesc('Incorrect current password.'),
        ];
    }
}
