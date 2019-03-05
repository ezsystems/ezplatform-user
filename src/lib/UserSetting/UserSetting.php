<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUser\UserSetting;

use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * @property string $identifier
 * @property string $name
 * @property string $description
 * @property string $value
 */
class UserSetting extends ValueObject
{
    /** @var string */
    protected $identifier;

    /** @var string */
    protected $name;

    /** @var string */
    protected $description;

    /** @var string */
    protected $value;
}
