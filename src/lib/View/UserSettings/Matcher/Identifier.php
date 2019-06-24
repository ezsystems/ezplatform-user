<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUser\View\UserSettings\Matcher;

use eZ\Publish\Core\MVC\Symfony\Matcher\ViewMatcherInterface;
use eZ\Publish\Core\MVC\Symfony\View\View;
use EzSystems\EzPlatformUser\View\UserSettings\UpdateView;

/**
 * Match based on the user setting identifier.
 */
class Identifier implements ViewMatcherInterface
{
    /** @var string[] */
    private $identifiers = [];

    /**
     * {@inheritdoc}
     */
    public function setMatchingConfig($matchingConfig): void
    {
        $this->identifiers = (array)$matchingConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function match(View $view): bool
    {
        if (!$view instanceof UpdateView || $view->getUserSetting() === null) {
            return false;
        }

        return \in_array($view->getUserSetting()->identifier, $this->identifiers);
    }
}
