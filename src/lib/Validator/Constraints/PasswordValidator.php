<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUser\Validator\Constraints;

use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\User\PasswordValidationContext;
use EzSystems\EzPlatformContentForms\Validator\ValidationErrorsProcessor;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PasswordValidator extends ConstraintValidator
{
    /** @var \eZ\Publish\API\Repository\UserService */
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!\is_string($value) || empty($value)) {
            return;
        }

        $passwordValidationContext = new PasswordValidationContext([
            'contentType' => $constraint->contentType,
            'user' => $constraint->user,
        ]);

        $validationErrors = $this->userService->validatePassword(
            $value,
            $passwordValidationContext
        );
        if (!empty($validationErrors)) {
            $validationErrorsProcessor = $this->createValidationErrorsProcessor();
            $validationErrorsProcessor->processValidationErrors($validationErrors);
        }
    }

    protected function createValidationErrorsProcessor(): ValidationErrorsProcessor
    {
        return new ValidationErrorsProcessor($this->context);
    }
}
