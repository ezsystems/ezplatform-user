<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUser\View\UserSettings;

use eZ\Publish\Core\MVC\Symfony\View\Builder\ViewBuilder;
use eZ\Publish\Core\MVC\Symfony\View\Configurator;
use eZ\Publish\Core\MVC\Symfony\View\ParametersInjector;
use EzSystems\EzPlatformUser\UserSetting\UserSettingService;

class UpdateViewBuilder implements ViewBuilder
{
    /** @var \EzSystems\EzPlatformUser\UserSetting\UserSettingService */
    private $userSettingService;

    /** @var \eZ\Publish\Core\MVC\Symfony\View\Configurator */
    private $viewConfigurator;

    /** @var \eZ\Publish\Core\MVC\Symfony\View\ParametersInjector */
    private $viewParametersInjector;

    /**
     * @param \EzSystems\EzPlatformUser\UserSetting\UserSettingService $userSettingService
     * @param \eZ\Publish\Core\MVC\Symfony\View\Configurator $viewConfigurator
     * @param \eZ\Publish\Core\MVC\Symfony\View\ParametersInjector $viewParametersInjector
     */
    public function __construct(
        UserSettingService $userSettingService,
        Configurator $viewConfigurator,
        ParametersInjector $viewParametersInjector
    ) {
        $this->userSettingService = $userSettingService;
        $this->viewConfigurator = $viewConfigurator;
        $this->viewParametersInjector = $viewParametersInjector;
    }

    /**
     * {@inheritdoc}
     */
    public function matches($argument): bool
    {
        return 'EzSystems\EzPlatformUserBundle\Controller\UserSettingsController::updateAction' === $argument;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(array $parameters): UpdateView
    {
        $view = new UpdateView();

        $view->setUserSetting($this->userSettingService->getUserSetting($parameters['identifier']));
        $this->viewParametersInjector->injectViewParameters($view, $parameters);
        $this->viewConfigurator->configure($view);

        return $view;
    }
}
