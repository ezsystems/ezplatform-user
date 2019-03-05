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
 * Configuration parser for user preferences.
 *
 * Example configuration:
 * ```yaml
 * ezpublish:
 *   system:
 *      default: # configuration per siteaccess or siteaccess group
 *          user_preferences:
 *              additional_translations: ['en_US', 'en_GB']
 *              short_datetime_format:
 *                  date_format: 'dd/mm/yyy'
 *                  time_format: 'hh:mm'
 *              full_datetime_format:
 *                  date_format: 'dd/mm/yyy'
 *                  time_format: 'hh:mm'
 *              allowed_short_date_formats:
 *                  'label for dd/MM/yyyy' ; 'dd/MM/yyyy"
 *                  'label for MM/dd/yyyy' ; 'MM/dd/yyyy"
 *              allowed_short_time_formats:
 *                  'label for HH:mm' : 'HH:mm",
 *                  'label for hh:mm a' : 'hh:mm a'
 *              allowed_full_date_formats:
 *                  'label for dd/MM/yyyy' ; 'dd/MM/yyyy"
 *                  'label for MM/dd/yyyy' ; 'MM/dd/yyyy"
 *              allowed_full_time_formats:
 *                  'label for HH:mm' : 'HH:mm",
 *                  'label for hh:mm a' : 'hh:mm a'
 * ```
 */
class UserPreferences extends AbstractParser
{
    /**
     * {@inheritdoc}
     */
    public function addSemanticConfig(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->arrayNode('user_preferences')
                ->info('User Preferences configuration.')
                ->children()
                    ->arrayNode('additional_translations')
                        ->info('Additional translations to display on the preferred language list.')
                        ->example(['en_US', 'en_GB'])
                        ->prototype('scalar')->end()
                    ->end()
                    ->arrayNode('full_datetime_format')
                        ->children()
                            ->scalarNode('date_format')
                                ->info('The date format')
                                ->example('dd/MM/yyyy')
                            ->end()
                            ->scalarNode('time_format')
                                ->info('The time format')
                                ->example('hh:mm')
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('short_datetime_format')
                        ->children()
                            ->scalarNode('date_format')
                                ->info('The date format')
                                ->example('dd/MM/yyyy')
                            ->end()
                            ->scalarNode('time_format')
                                ->info('The time format')
                                ->example('hh:mm')
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('allowed_short_date_formats')
                        ->info('List of allowed short date formats.')
                        ->example(['label dd/MM/yyyy' => 'dd/MM/yyyy', 'label for mm/dd/yyyy' => 'mm/dd/yyyy '])
                        ->prototype('scalar')->end()
                    ->end()
                    ->arrayNode('allowed_short_time_formats')
                        ->info('List of allowed short time formats.')
                        ->example(['label hh:mm' => 'hh:mm', 'label for HH:mm' => 'HH:mm'])
                        ->prototype('scalar')->end()
                    ->end()
                    ->arrayNode('allowed_full_date_formats')
                        ->info('List of allowed full date formats.')
                        ->example(['label dd/MM/yyyy' => 'dd/MM/yyyy', 'label for mm/dd/yyyy' => 'mm/dd/yyyy '])
                        ->prototype('scalar')->end()
                    ->end()
                    ->arrayNode('allowed_full_time_formats')
                        ->info('List of allowed full time formats.')
                        ->example(['label hh:mm' => 'hh:mm', 'label for HH:mm' => 'HH:mm'])
                        ->prototype('scalar')->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer): void
    {
        if (empty($scopeSettings['user_preferences'])) {
            return;
        }

        $settings = $scopeSettings['user_preferences'];
        foreach ($settings as $key => $value) {
            $contextualizer->setContextualParameter(
                "user_preferences.$key",
                $currentScope,
                $value
            );
        }
    }
}
