<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformUserBundle;

use EzSystems\EzPlatformUserBundle\DependencyInjection\Compiler\UserSetting;
use EzSystems\EzPlatformUserBundle\DependencyInjection\Configuration\Parser\ChangePassword;
use EzSystems\EzPlatformUserBundle\DependencyInjection\Configuration\Parser\Pagination;
use EzSystems\EzPlatformUserBundle\DependencyInjection\Configuration\Parser\UserPreferences;
use EzSystems\EzPlatformUserBundle\DependencyInjection\Configuration\Parser\UserRegistration;
use EzSystems\EzPlatformUserBundle\DependencyInjection\Configuration\Parser\Security;
use EzSystems\EzPlatformUserBundle\DependencyInjection\Configuration\Parser\UserSettingsUpdateView;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EzPlatformUserBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        /** @var \eZ\Bundle\EzPublishCoreBundle\DependencyInjection\EzPublishCoreExtension $core */
        $core = $container->getExtension('ezpublish');
        $core->addConfigParser(new Security());
        $core->addConfigParser(new ChangePassword());
        $core->addConfigParser(new Pagination());
        $core->addConfigParser(new UserRegistration());
        $core->addConfigParser(new UserPreferences());
        $core->addConfigParser(new UserSettingsUpdateView());

        $container->addCompilerPass(new UserSetting\ValueDefinitionPass());
        $container->addCompilerPass(new UserSetting\FormMapperPass());
        $container->addCompilerPass(new UserSetting\ViewBuilderRegistryPass());

        $core->addDefaultSettings(__DIR__ . '/Resources/config', ['ezplatform_default_settings.yml']);
    }
}
