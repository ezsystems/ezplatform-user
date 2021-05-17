<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUser\ExceptionHandler;

class NullActionResultHandler implements ActionResultHandler
{
    public function error(
        string $message,
        array $parameters = [],
        ?string $domain = null,
        ?string $locale = null
    ): void {
    }

    public function success(
        string $message,
        array $parameters = [],
        ?string $domain = null,
        ?string $locale = null
    ): void {
    }
}
