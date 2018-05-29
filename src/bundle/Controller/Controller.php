<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformUserBundle\Controller;

use eZ\Publish\API\Repository\Values\Content\Location;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\HttpFoundation\RedirectResponse;

abstract class Controller extends BaseController
{
    public function performAccessCheck()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
    }
}
