<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUser\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Password extends Constraint
{
    /** @var string */
    public $message = 'ez.user.password.invalid';

    /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentType|null */
    public $contentType;

    /** @var \eZ\Publish\API\Repository\Values\User\User|null */
    public $user;

    /**
     * {@inheritdoc}
     */
    public function getTargets(): array
    {
        return [self::CLASS_CONSTRAINT, self::PROPERTY_CONSTRAINT];
    }
}
