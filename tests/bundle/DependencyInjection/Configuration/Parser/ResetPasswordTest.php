<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUserBundle\Tests\DependencyInjection\Configuration\Parser;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\EzPublishCoreExtension;
use eZ\Bundle\EzPublishCoreBundle\Tests\DependencyInjection\Configuration\Parser\AbstractParserTestCase;
use EzSystems\EzPlatformUserBundle\DependencyInjection\Configuration\Parser\ResetPassword;
use EzSystems\EzPlatformUserBundle\DependencyInjection\EzPlatformUserExtension;

final class ResetPasswordTest extends AbstractParserTestCase
{
    protected function getContainerExtensions(): array
    {
        return [
            new EzPublishCoreExtension([
                new ResetPassword(),
            ]),
            new EzPlatformUserExtension(),
        ];
    }

    protected function getMinimalConfiguration(): array
    {
        return [
            'system' => [
                'default' => [
                    'user_reset_password' => [
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
            'user_reset_password.templates.form',
            'default/path/template.html.twig',
            'ezdemo_site'
        );
    }

    public function testOverwrittenConfig()
    {
        $this->load([
            'system' => [
                'ezdemo_site' => [
                    'user_reset_password' => [
                        'templates' => [
                            'form' => '@yourOwnBundle/path/to/template.html.twig',
                            'invalid_link' => '@yourOwnBundle/path/to/invalid_link.html.twig',
                            'success' => '@yourOwnBundle/path/to/success.html.twig'
                        ]
                    ],
                ],
            ],
        ]);

        $this->assertConfigResolverParameterValue(
            'user_reset_password.templates.form',
            '@yourOwnBundle/path/to/template.html.twig',
            'ezdemo_site'
        );
        $this->assertConfigResolverParameterValue(
            'user_reset_password.templates.invalid_link',
            '@yourOwnBundle/path/to/invalid_link.html.twig',
            'ezdemo_site'
        );
        $this->assertConfigResolverParameterValue(
            'user_reset_password.templates.success',
            '@yourOwnBundle/path/to/success.html.twig',
            'ezdemo_site'
        );
    }
}
