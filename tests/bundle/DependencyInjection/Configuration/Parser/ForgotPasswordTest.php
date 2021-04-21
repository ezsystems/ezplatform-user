<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Platform\Tests\Bundle\OAuth2Client\DependencyInjection\Configuration\Parser;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\EzPublishCoreExtension;
use eZ\Bundle\EzPublishCoreBundle\Tests\DependencyInjection\Configuration\Parser\AbstractParserTestCase;
use EzSystems\EzPlatformUserBundle\DependencyInjection\Configuration\Parser\ForgotPassword;
use EzSystems\EzPlatformUserBundle\DependencyInjection\EzPlatformUserExtension;

final class ForgotPasswordTest extends AbstractParserTestCase
{
    protected function getContainerExtensions(): array
    {
        return [
            new EzPublishCoreExtension([
                new ForgotPassword(),
            ]),
            new EzPlatformUserExtension(),
        ];
    }

    protected function getMinimalConfiguration(): array
    {
        return [
            'system' => [
                'default' => [
                    'user_forgot_password' => [
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
            'user_forgot_password.templates.form',
            'default/path/template.html.twig',
            'ezdemo_site'
        );
    }

    public function testOverwrittenConfig()
    {
        $this->load([
            'system' => [
                'ezdemo_site' => [
                    'user_forgot_password' => [
                        'templates' => [
                            'form' => '@yourOwnBundle/path/to/template.html.twig',
                            'mail' => '@yourOwnBundle/path/to/mail.html.twig',
                        ]
                    ],
                    'user_forgot_password_success' => [
                        'templates' => [
                            'form' => '@yourOwnBundle/path/to/template_success.html.twig',
                        ]
                    ],
                    'user_forgot_password_login' => [
                        'templates' => [
                            'form' => '@yourOwnBundle/path/to/template_login.html.twig',
                        ]
                    ],
                ],
            ],
        ]);

        $this->assertConfigResolverParameterValue(
            'user_forgot_password.templates.form',
            '@yourOwnBundle/path/to/template.html.twig',
            'ezdemo_site'
        );
        $this->assertConfigResolverParameterValue(
            'user_forgot_password.templates.mail',
            '@yourOwnBundle/path/to/mail.html.twig',
            'ezdemo_site'
        );
        $this->assertConfigResolverParameterValue(
            'user_forgot_password_success.templates.form',
            '@yourOwnBundle/path/to/template_success.html.twig',
            'ezdemo_site'
        );
        $this->assertConfigResolverParameterValue(
            'user_forgot_password_login.templates.form',
            '@yourOwnBundle/path/to/template_login.html.twig',
            'ezdemo_site'
        );
    }
}
