<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUser\Tests\UserSetting;

use EzSystems\EzPlatformUser\UserSetting\ValueDefinitionRegistry;
use EzSystems\EzPlatformUser\UserSetting\ValueDefinitionInterface;
use PHPUnit\Framework\TestCase;

class ValueDefinitionRegistryTest extends TestCase
{
    public function testGetValueDefinitions()
    {
        $definitions = [
            'foo' => $this->createMock(ValueDefinitionInterface::class),
            'bar' => $this->createMock(ValueDefinitionInterface::class),
            'baz' => $this->createMock(ValueDefinitionInterface::class)
        ];

        $registry = new ValueDefinitionRegistry($definitions);

        $this->assertEquals($definitions, $registry->getValueDefinitions());
    }

    public function testAddValueDefinition()
    {
        $foo = $this->createMock(ValueDefinitionInterface::class);

        $registry = new ValueDefinitionRegistry([]);
        $registry->addValueDefinition('foo', $foo);

        $this->assertEquals(['foo' => $foo], $registry->getValueDefinitions());
    }

    public function testHasValueDefinition()
    {
        $registry = new ValueDefinitionRegistry([
            'foo' => $this->createMock(ValueDefinitionInterface::class),
        ]);

        $this->assertTrue($registry->hasValueDefinition('foo'));
        $this->assertFalse($registry->hasValueDefinition('bar'));
    }

    public function testGetValueDefinition()
    {
        $foo = $this->createMock(ValueDefinitionInterface::class);

        $registry = new ValueDefinitionRegistry([
            'foo' => $foo,
        ]);

        $this->assertEquals($foo, $registry->getValueDefinition('foo'));
    }
}
