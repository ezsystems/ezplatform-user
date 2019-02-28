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
    /** @var \EzSystems\EzPlatformAdminUi\UserSetting\ValueDefinitionInterface[] */
    protected $valueDefinitions;

    /**
     * @param \EzSystems\EzPlatformAdminUi\UserSetting\ValueDefinitionInterface[] $valueDefinitions
     */
    public function __construct(array $valueDefinitions = [])
    {
        $this->valueDefinitions = $valueDefinitions;
    }

    /**
     * @param string $identifier
     * @param \EzSystems\EzPlatformUser\UserSetting\ValueDefinitionInterface $valueDefinition
     */
    public function addValueDefinition(
        string $identifier,
        AdminUiUserSettings\ValueDefinitionInterface $valueDefinition
    ): void {
        $this->valueDefinitions[$identifier] = $valueDefinition;
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

        return $this->valueDefinitions[$identifier];
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
        return $this->valueDefinitions;
    }
}
