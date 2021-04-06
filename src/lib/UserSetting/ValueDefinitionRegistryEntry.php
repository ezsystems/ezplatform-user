<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUser\UserSetting;

use Ibexa\Contracts\User\UserSetting\ValueDefinitionInterface;

/**
 * @internal
 */
final class ValueDefinitionRegistryEntry
{
    /** @var \Ibexa\Contracts\User\UserSetting\ValueDefinitionInterface */
    private $definition;

    /** @var int */
    private $priority;

    /**
     * @param \Ibexa\Contracts\User\UserSetting\ValueDefinitionInterface $definition
     * @param int $priority
     */
    public function __construct(ValueDefinitionInterface $definition, int $priority = 0)
    {
        $this->definition = $definition;
        $this->priority = $priority;
    }

    /**
     * @return \Ibexa\Contracts\User\UserSetting\ValueDefinitionInterface
     */
    public function getDefinition(): ValueDefinitionInterface
    {
        return $this->definition;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }
}
