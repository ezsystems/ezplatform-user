<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUser\Tests\UserSetting;

use eZ\Publish\API\Repository\UserPreferenceService;
use eZ\Publish\API\Repository\Values\UserPreference\UserPreference;
use EzSystems\EzPlatformUser\UserSetting\UserSetting;
use EzSystems\EzPlatformUser\UserSetting\UserSettingService;
use Ibexa\Contracts\User\UserSetting\ValueDefinitionInterface;
use EzSystems\EzPlatformUser\UserSetting\ValueDefinitionRegistry;
use PHPUnit\Framework\TestCase;

class UserSettingServiceTest extends TestCase
{
    public function testCountUserSettings(): void
    {
        $userPreferenceService = $this->createMock(UserPreferenceService::class);
        $valueRegistry = $this->createMock(ValueDefinitionRegistry::class);
        $valueRegistry->method('countValueDefinitions')->willReturn(2);
        $userSettingService = new UserSettingService($userPreferenceService, $valueRegistry);

        $this->assertEquals(2, $userSettingService->countUserSettings());
    }

    public function testLoadUserSettings(): void
    {
        $userPreferenceService = $this->createMock(UserPreferenceService::class);
        $userPreferenceService->method('getUserPreference')
            ->willReturnMap([
                ['identifier_1', new UserPreference(['value' => '1'])],
                ['identifier_2', new UserPreference(['value' => '2'])],
                ['identifier_3', new UserPreference(['value' => '3'])],
                ['identifier_4', new UserPreference(['value' => '4'])],
            ]
        );

        $valueRegistry = $this->createMock(ValueDefinitionRegistry::class);
        $valueRegistry->method('getValueDefinitions')->willReturn([
            'identifier_1' => $this->getValueDefinition('name_1', 'description_1'),
            'identifier_2' => $this->getValueDefinition('name_2', 'description_2'),
            'identifier_3' => $this->getValueDefinition('name_3', 'description_3'),
            'identifier_4' => $this->getValueDefinition('name_4', 'description_4'),
        ]);
        $userSettingService = new UserSettingService($userPreferenceService, $valueRegistry);

        $settings = $userSettingService->loadUserSettings(1, 2);
        $expected = [
            new UserSetting([
                'identifier' => 'identifier_2',
                'name' => 'name_2',
                'description' => 'description_2',
                'value' => '2',
            ]),
            new UserSetting([
                'identifier' => 'identifier_3',
                'name' => 'name_3',
                'description' => 'description_3',
                'value' => '3',
            ]),
        ];
        $this->assertEquals($expected, $settings);
    }

    /**
     * @param string $name
     * @param string $description
     *
     * @return \Ibexa\Contracts\User\UserSetting\ValueDefinitionInterface
     */
    private function getValueDefinition(string $name = 'name', string $description = 'description'): ValueDefinitionInterface
    {
        $valueDefinition = $this->createMock(ValueDefinitionInterface::class);
        $valueDefinition->method('getName')->willReturn($name);
        $valueDefinition->method('getDescription')->willReturn($description);

        return $valueDefinition;
    }
}
