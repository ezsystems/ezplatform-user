<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUser\ConfigResolver;

/**
 * Loads the content type used by user registration.
 */
interface RegistrationContentTypeLoader
{
    /**
     * Gets the Content Type used by user registration.
     *
     * @return \eZ\Publish\API\Repository\Values\ContentType\ContentType
     */
    public function loadContentType();
}
