<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformUser\Form\Processor;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\UserService;
use EzSystems\EzPlatformUser\Form\Data\UserRegisterData;
use EzSystems\EzPlatformUser\Form\UserFormEvents;
use EzSystems\EzPlatformContentForms\Event\FormActionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

/**
 * Listens for and processes User register events.
 */
class UserRegisterFormProcessor implements EventSubscriberInterface
{
    /** @var \eZ\Publish\API\Repository\UserService */
    private $userService;

    /** @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface */
    private $urlGenerator;

    /** @var \eZ\Publish\Core\Repository\Repository */
    private $repository;

    public function __construct(Repository $repository, UserService $userService, RouterInterface $router)
    {
        $this->userService = $userService;
        $this->urlGenerator = $router;
        $this->repository = $repository;
    }

    public static function getSubscribedEvents()
    {
        return [
            UserFormEvents::USER_REGISTER => ['processRegister', 20],
        ];
    }

    /**
     * @param \EzSystems\EzPlatformContentForms\Event\FormActionEvent $event
     *
     * @throws \Exception
     */
    public function processRegister(FormActionEvent $event)
    {
        /** @var UserRegisterData $data */
        if (!($data = $event->getData()) instanceof UserRegisterData) {
            return;
        }
        $form = $event->getForm();

        $this->createUser($data, $form->getConfig()->getOption('languageCode'));

        $redirectUrl = $this->urlGenerator->generate('ezplatform.user.register_confirmation');
        $event->setResponse(new RedirectResponse($redirectUrl));
        $event->stopPropagation();
    }

    /**
     * @param \EzSystems\EzPlatformUser\Form\Data\UserRegisterData $data
     * @param $languageCode
     *
     * @return \eZ\Publish\API\Repository\Values\User\User
     *
     * @throws \Exception
     */
    private function createUser(UserRegisterData $data, $languageCode)
    {
        foreach ($data->fieldsData as $fieldDefIdentifier => $fieldData) {
            $data->setField($fieldDefIdentifier, $fieldData->value, $languageCode);
        }

        return $this->repository->sudo(
            function () use ($data) {
                return $this->userService->createUser($data, $data->getParentGroups());
            }
        );
    }
}
