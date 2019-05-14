<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUser\UserSetting;

use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use EzSystems\EzPlatformAdminUi\UserSetting as AdminUiUserSettings;

/**
 * @internal
 */
class ValueDefinitionRegistry
{
    /** @var \EzSystems\EzPlatformUser\UserSetting\ValueDefinitionRegistryEntry[] */
    protected $valueDefinitions;

    /**
     * @param \EzSystems\EzPlatformUser\UserSetting\ValueDefinitionRegistryEntry[] $valueDefinitions
     */
    public function __construct(array $valueDefinitions = [])
    {
        $this->valueDefinitions = [];
        foreach ($valueDefinitions as $identifier => $valueDefinition) {
            $this->valueDefinitions[$identifier] = new ValueDefinitionRegistryEntry($valueDefinition);
        }
    }

    /**
     * @param string $identifier
     * @param AdminUiUserSettings\ValueDefinitionInterface $valueDefinition
     * @param int $priority
     */
    public function addValueDefinition(
        string $identifier,
        AdminUiUserSettings\ValueDefinitionInterface $valueDefinition,
        int $priority = 0
    ): void {
        $this->valueDefinitions[$identifier] = new ValueDefinitionRegistryEntry($valueDefinition, $priority);
    }

    /**
     * @param string $identifier
     *
     * @return \EzSystems\EzPlatformUser\UserSetting\ValueDefinitionInterface
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function getValueDefinition(string $identifier): AdminUiUserSettings\ValueDefinitionInterface
    {
        if (!isset($this->valueDefinitions[$identifier])) {
            throw new InvalidArgumentException(
                '$identifier',
                sprintf('There is no ValueDefinition service registered for \'%s\' identifier', $identifier)
            );
        }

        return $this->valueDefinitions[$identifier]->getDefinition();
    }

    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function hasValueDefinition(string $identifier): bool
    {
        return isset($this->valueDefinitions[$identifier]);
    }

    /**
     * @return \EzSystems\EzPlatformAdminUi\UserSetting\ValueDefinitionInterface[]
     */
    public function getValueDefinitions(): array
    {
        uasort($this->valueDefinitions, function (ValueDefinitionRegistryEntry $a, ValueDefinitionRegistryEntry $b) {
            return $b->getPriority() <=> $a->getPriority();
        });

        return array_map(function (ValueDefinitionRegistryEntry $entry) {
            return $entry->getDefinition();
        }, $this->valueDefinitions);
    }

    /**
     * @return int
     */
    public function countValueDefinitions(): int
    {
        return \count($this->valueDefinitions);
    }
}
