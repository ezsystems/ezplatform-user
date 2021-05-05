<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUserBundle\DependencyInjection\Configuration\Parser;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\AbstractParser;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

final class ForgotPassword extends AbstractParser
{
    public function addSemanticConfig(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
            ->arrayNode('user_forgot_password')
                ->info('User forgot password configuration')
                ->children()
                    ->arrayNode('templates')
                        ->children()
                            ->scalarNode('form')
                                ->info('Template to use for forgot password form rendering.')
                            ->end()
                            ->scalarNode('mail')
                                ->info('Template to use for forgot password mail with reset link.')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->arrayNode('user_forgot_password_success')
                ->info('User forgot password success configuration')
                ->children()
                    ->arrayNode('templates')
                        ->children()
                            ->scalarNode('form')
                                ->info('Template to use for success forgot password form rendering.')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->arrayNode('user_forgot_password_login')
                ->info('User forgot password with login configuration')
                ->children()
                    ->arrayNode('templates')
                        ->children()
                            ->scalarNode('form')
                                ->info('Template to use for forgot password with login form .')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end()
        ;
    }

    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer)
    {
        if (!empty($scopeSettings['user_forgot_password'])) {
            $settings = $scopeSettings['user_forgot_password']['templates'];
            if (!empty($settings['form'])) {
                $contextualizer->setContextualParameter(
                    'user_forgot_password.templates.form',
                    $currentScope,
                    $settings['form']
                );
            }
            if (!empty($settings['mail'])) {
                $contextualizer->setContextualParameter(
                    'user_forgot_password.templates.mail',
                    $currentScope,
                    $settings['mail']
                );
            }
        }

        if (!empty($scopeSettings['user_forgot_password_success'])) {
            $settings = $scopeSettings['user_forgot_password_success']['templates'];
            $contextualizer->setContextualParameter(
                'user_forgot_password_success.templates.form',
                $currentScope,
                $settings['form']
            );
        }

        if (!empty($scopeSettings['user_forgot_password_login'])) {
            $settings = $scopeSettings['user_forgot_password_login']['templates'];
            $contextualizer->setContextualParameter(
                'user_forgot_password_login.templates.form',
                $currentScope,
                $settings['form']
            );
        }
    }
}
