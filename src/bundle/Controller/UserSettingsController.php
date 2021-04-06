<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUserBundle\Controller;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzSystems\EzPlatformUser\ExceptionHandler\ActionResultHandler;
use EzSystems\EzPlatformUser\Form\Data\UserSettingUpdateData;
use EzSystems\EzPlatformUser\Form\SubmitHandler;
use EzSystems\EzPlatformUser\Form\Type\UserSettingUpdateType;
use EzSystems\EzPlatformUser\Pagination\Pagerfanta\UserSettingsAdapter;
use EzSystems\EzPlatformUser\UserSetting\UserSettingService;
use EzSystems\EzPlatformUser\UserSetting\ValueDefinitionRegistry;
use EzSystems\EzPlatformUser\View\UserSettings\ListView;
use EzSystems\EzPlatformUser\View\UserSettings\UpdateView;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Util\StringUtil;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserSettingsController extends Controller
{
    /** @var \Symfony\Component\Form\FormFactory */
    private $formFactory;

    /** @var \EzSystems\EzPlatformUser\Form\SubmitHandler */
    private $submitHandler;

    /** @var \EzSystems\EzPlatformUser\UserSetting\UserSettingService */
    private $userSettingService;

    /** @var \EzSystems\EzPlatformUser\UserSetting\ValueDefinitionRegistry */
    private $valueDefinitionRegistry;

    /** @var \EzSystems\EzPlatformUser\ExceptionHandler\ActionResultHandler */
    private $actionResultHandler;

    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    public function __construct(
        FormFactory $formFactory,
        SubmitHandler $submitHandler,
        UserSettingService $userSettingService,
        ValueDefinitionRegistry $valueDefinitionRegistry,
        ActionResultHandler $actionResultHandler,
        ConfigResolverInterface $configResolver
    ) {
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->userSettingService = $userSettingService;
        $this->valueDefinitionRegistry = $valueDefinitionRegistry;
        $this->actionResultHandler = $actionResultHandler;
        $this->configResolver = $configResolver;
    }

    /**
     * @param int $page
     *
     * @return \EzSystems\EzPlatformUser\View\UserSettings\ListView
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     */
    public function listAction(int $page = 1): ListView
    {
        $pagerfanta = new Pagerfanta(
            new UserSettingsAdapter($this->userSettingService)
        );

        $pagerfanta->setMaxPerPage($this->configResolver->getParameter('pagination_user.user_settings_limit'));
        $pagerfanta->setCurrentPage(min($page, $pagerfanta->getNbPages()));

        return new ListView(null, [
            'pager' => $pagerfanta,
            'value_definitions' => $this->valueDefinitionRegistry->getValueDefinitions(),
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \EzSystems\EzPlatformUser\View\UserSettings\UpdateView $view
     *
     * @return \EzSystems\EzPlatformUser\View\UserSettings\UpdateView|\Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, UpdateView $view)
    {
        $userSetting = $view->getUserSetting();

        $data = new UserSettingUpdateData($userSetting->identifier, $userSetting->value);

        $form = $this->getUpdateUserSettingForm($userSetting->identifier, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (UserSettingUpdateData $data) {
                $this->userSettingService->setUserSetting($data->getIdentifier(), $data->getValue());

                $this->actionResultHandler->success(
                    /** @Desc("User setting '%identifier%' updated.") */
                    'user_setting.update.success',
                    ['%identifier%' => $data->getIdentifier()],
                    'user_settings'
                );

                return new RedirectResponse($this->generateUrl('ezplatform.user_settings.list'));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        $view->addParameters([
            'form' => $form->createView(),
        ]);

        return $view;
    }

    private function getUpdateUserSettingForm(
        string $userSettingIdentifier,
        UserSettingUpdateData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(UserSettingUpdateType::class);

        return $this->formFactory->createNamed(
            $name,
            UserSettingUpdateType::class,
            $data,
            ['user_setting_identifier' => $userSettingIdentifier]
        );
    }
}
