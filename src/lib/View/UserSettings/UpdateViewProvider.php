<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUser\View\UserSettings;

use eZ\Publish\Core\MVC\Symfony\Matcher\MatcherFactoryInterface;
use eZ\Publish\Core\MVC\Symfony\View\View;
use eZ\Publish\Core\MVC\Symfony\View\ViewProvider;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

class UpdateViewProvider implements ViewProvider
{
    /** @var \eZ\Publish\Core\MVC\Symfony\Matcher\MatcherFactoryInterface */
    protected $matcherFactory;

    /**
     * @param \eZ\Publish\Core\MVC\Symfony\Matcher\MatcherFactoryInterface $matcherFactory
     */
    public function __construct(MatcherFactoryInterface $matcherFactory)
    {
        $this->matcherFactory = $matcherFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getView(View $view)
    {
        if (($configHash = $this->matcherFactory->match($view)) === null) {
            return null;
        }

        return $this->buildUpdateSettingView($configHash);
    }

    /**
     * @param array $viewConfig
     *
     * @return \EzSystems\EzPlatformUser\View\UserSettings\UpdateView
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     */
    protected function buildUpdateSettingView(array $viewConfig): UpdateView
    {
        $view = new UpdateView();

        if (isset($viewConfig['template'])) {
            $view->setTemplateIdentifier($viewConfig['template']);
        }

        if (isset($viewConfig['controller'])) {
            $view->setControllerReference(new ControllerReference($viewConfig['controller']));
        }

        if (isset($viewConfig['params']) && \is_array($viewConfig['params'])) {
            $view->addParameters($viewConfig['params']);
        }

        return $view;
    }
}
