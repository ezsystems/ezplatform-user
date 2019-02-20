<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;

abstract class Controller extends BaseController
{
    public function performAccessCheck()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
    }
}
