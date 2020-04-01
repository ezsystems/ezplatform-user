<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUser\EventListener;

use EzSystems\EzPlatformUser\View;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ViewTemplatesListener implements EventSubscriberInterface
{
    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
    }

    public static function getSubscribedEvents(): array
    {
        return [MVCEvents::PRE_CONTENT_VIEW => 'setViewTemplates'];
    }

    /**
     * If the event's view has a defined template, sets the view's template identifier,
     * and the 'pagelayout' parameter.
     */
    public function setViewTemplates(PreContentViewEvent $event): void
    {
        $view = $event->getContentView();
        $pagelayout = $this->configResolver->getParameter('pagelayout');

        foreach ($this->getTemplatesMap() as $viewClass => $template) {
            if ($view instanceof $viewClass) {
                $view->setTemplateIdentifier($template);
                $view->addParameters(['pagelayout' => $pagelayout]);
                $view->addParameters(['page_layout' => $pagelayout]);
            }
        }
    }

    /**
     * @return string[]
     */
    private function getTemplatesMap(): array
    {
        return [
            View\ChangePassword\FormView::class => $this->configResolver->getParameter('user_change_password.templates.form'),
            View\ChangePassword\SuccessView::class => $this->configResolver->getParameter('user_change_password.templates.success'),
            View\ForgotPassword\FormView::class => $this->configResolver->getParameter('user_forgot_password.templates.form'),
            View\ForgotPassword\SuccessView::class => $this->configResolver->getParameter('user_forgot_password_success.templates.form'),
            View\ForgotPassword\LoginView::class => $this->configResolver->getParameter('user_forgot_password_login.templates.form'),
            View\ResetPassword\FormView::class => $this->configResolver->getParameter('user_reset_password.templates.form'),
            View\ResetPassword\InvalidLinkView::class => $this->configResolver->getParameter('user_reset_password.templates.invalid_link'),
            View\ResetPassword\SuccessView::class => $this->configResolver->getParameter('user_reset_password.templates.success'),
            View\UserSettings\ListView::class => $this->configResolver->getParameter('user_settings.templates.list'),
            View\Register\FormView::class => $this->configResolver->getParameter('user_registration.templates.form'),
            View\Register\ConfirmView::class => $this->configResolver->getParameter('user_registration.templates.confirmation'),
        ];
    }
}
