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

/**
 * Configuration parser for pagination limits declaration.
 *
 * Example configuration:
 * ```yaml
 * ezpublish:
 *   system:
 *      default: # configuration per siteaccess or siteaccess group
 *          pagination:
 *              user_settings_limit: 10
 * ```
 */
class Pagination extends AbstractParser
{
    /**
     * Adds semantic configuration definition.
     *
     * @param \Symfony\Component\Config\Definition\Builder\NodeBuilder $nodeBuilder Node just under ezpublish.system.<siteaccess>
     */
    public function addSemanticConfig(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
            ->arrayNode('pagination')
                ->info('System pagination configuration')
                ->children()
                    ->scalarNode('user_settings_limit')->isRequired()->end()
                ->end()
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer): void
    {
        if (empty($scopeSettings['pagination'])) {
            return;
        }

        $settings = $scopeSettings['pagination'];
        $keys = [
            'user_settings_limit',
        ];

        foreach ($keys as $key) {
            if (!isset($settings[$key]) || empty($settings[$key])) {
                continue;
            }

            $contextualizer->setContextualParameter(
                sprintf('pagination.%s', $key),
                $currentScope,
                $settings[$key]
            );
        }
    }
}
