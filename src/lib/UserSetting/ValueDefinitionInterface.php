<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUser\UserSetting;

use EzSystems\EzPlatformAdminUi\UserSetting as AdminUiUserSettings;

/**
 * Interface for displaying User Preferences in the Admin UI.
 *
 * User Preferences are not displayed by default unless
 * ValueDefinitionInterface implementation is provided.
 */
interface ValueDefinitionInterface extends AdminUiUserSettings\ValueDefinitionInterface
{
}
