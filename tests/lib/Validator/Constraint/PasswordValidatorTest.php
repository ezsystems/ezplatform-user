<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUser\Tests\Validator\Constraint;

use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\User\PasswordValidationContext;
use eZ\Publish\API\Repository\Values\User\User;
use eZ\Publish\Core\FieldType\ValidationError;
use EzSystems\EzPlatformUser\Validator\Constraints\Password;
use EzSystems\EzPlatformUser\Validator\Constraints\PasswordValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class PasswordValidatorTest extends TestCase
{
    /** @var \eZ\Publish\API\Repository\UserService|\PHPUnit\Framework\MockObject\MockObject */
    private $userService;

    /** @var \PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\Validator\Context\ExecutionContextInterface */
    private $executionContext;

    /** @var \EzSystems\EzPlatformUser\Validator\Constraints\PasswordValidator */
    private $validator;

    protected function setUp(): void
    {
        $this->userService = $this->createMock(UserService::class);
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new PasswordValidator($this->userService);
        $this->validator->initialize($this->executionContext);
    }

    /**
     * @dataProvider dataProviderForValidateNotSupportedValueType
     */
    public function testValidateShouldBeSkipped($value): void
    {
        $this->userService
            ->expects($this->never())
            ->method('validatePassword');

        $this->executionContext
            ->expects($this->never())
            ->method('buildViolation');

        $this->validator->validate($value, new Password());
    }

    public function testValid(): void
    {
        $password = 'pass';
        $contentType = $this->createMock(ContentType::class);
        $user = $this->createMock(User::class);

        $this->userService
            ->expects(self::once())
            ->method('validatePassword')
            ->willReturnCallback(
                function (string $actualPassword, PasswordValidationContext $actualContext) use (
                    $password,
                    $contentType,
                    $user
                ): array {
                    self::assertEquals($password, $actualPassword);
                    self::assertInstanceOf(PasswordValidationContext::class, $actualContext);
                    self::assertSame($contentType, $actualContext->contentType);
                    self::assertSame($user, $actualContext->user);

                    return [];
                }
            );

        $this->executionContext
            ->expects($this->never())
            ->method('buildViolation');

        $this->validator->validate(
            $password,
            new Password([
                'contentType' => $contentType,
                'user' => $user,
            ])
        );
    }

    public function testInvalid(): void
    {
        $contentType = $this->createMock(ContentType::class);
        $password = 'pass';
        $errorParameter = 'foo';
        $errorMessage = 'error';

        $this->userService
            ->expects($this->once())
            ->method('validatePassword')
            ->willReturnCallback(function (string $actualPassword, PasswordValidationContext $actualContext) use (
                $password,
                $contentType,
                $errorMessage,
                $errorParameter
            ): array {
                $this->assertEquals($password, $actualPassword);
                $this->assertInstanceOf(PasswordValidationContext::class, $actualContext);
                $this->assertSame($contentType, $actualContext->contentType);

                return [
                    new ValidationError($errorMessage, null, ['%foo%' => $errorParameter]),
                ];
            });

        $constraintViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $this->executionContext
            ->expects($this->once())
            ->method('buildViolation')
            ->willReturn($constraintViolationBuilder);
        $this->executionContext
            ->expects($this->once())
            ->method('buildViolation')
            ->with($errorMessage)
            ->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder
            ->expects($this->once())
            ->method('setParameters')
            ->with(['%foo%' => $errorParameter])
            ->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder
            ->expects($this->once())
            ->method('addViolation');

        $this->validator->validate('pass', new Password([
            'contentType' => $contentType,
        ]));
    }

    public function dataProviderForValidateNotSupportedValueType(): array
    {
        return [
            [new \stdClass()],
            [null],
            [''],
        ];
    }
}
