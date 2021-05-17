<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUser\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

class BaseSubmitHandler implements SubmitHandler
{
    public function handle(FormInterface $form, callable $handler): ?Response
    {
        $data = $form->getData();

        if ($form->isValid()) {
            return $handler($data);
        }

        return null;
    }
}
