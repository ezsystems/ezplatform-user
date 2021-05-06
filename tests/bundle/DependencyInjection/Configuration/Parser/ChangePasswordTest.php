<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Platform\Tests\Bundle\OAuth2Client\DependencyInjection\Configuration\Parser;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\EzPublishCoreExtension;
use eZ\Bundle\EzPublishCoreBundle\Tests\DependencyInjection\Configuration\Parser\AbstractParserTestCase;
use EzSystems\EzPlatformUserBundle\DependencyInjection\Configuration\Parser\ChangePassword;
use EzSystems\EzPlatformUserBundle\DependencyInjection\EzPlatformUserExtension;

final class ChangePasswordTest extends AbstractParserTestCase
{
    protected function getContainerExtensions(): array
    {
        return [
            new EzPublishCoreExtension([
                new ChangePassword(),
            ]),
            new EzPlatformUserExtension(),
        ];
    }

    protected function getMinimalConfiguration(): array
    {
        return [
            'system' => [
                'default' => [
                    'user_change_password' => [
                        'templates' => [
                            'form' => 'default/path/template.html.twig',
                        ]
                    ],
                ],
            ],
        ];
    }

    public function testDefaultSettings(): void
    {
        $this->load();

        $this->assertConfigResolverParameterValue(
            'user_change_password.templates.form',
            'default/path/template.html.twig',
            'ezdemo_site'
        );
    }

    public function testOverwrittenConfig()
    {
        $this->load([
            'system' => [
                'ezdemo_site' => [
                    'user_change_password' => [
                        'templates' => [
                            'form' => '@yourOwnBundle/path/to/template.html.twig',
                            'success' => '@yourOwnBundle/path/to/success.html.twig',
                        ]
                    ],
                ],
            ],
        ]);

        $this->assertConfigResolverParameterValue(
            'user_change_password.templates.form',
            '@yourOwnBundle/path/to/template.html.twig',
            'ezdemo_site'
        );
        $this->assertConfigResolverParameterValue(
            'user_change_password.templates.success',
            '@yourOwnBundle/path/to/success.html.twig',
            'ezdemo_site'
        );
    }
}
