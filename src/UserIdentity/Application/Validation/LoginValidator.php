<?php

declare(strict_types=1);

namespace UserIdentity\Application\Validation;

use Common\Application\Validation\RequestParamValidator;
use Common\Application\Validation\Result as ValidationResult;
use Symfony\Contracts\Translation\TranslatorInterface;
use UserIdentity\Application\Specification\AreValidCredentialsSpecification;
use UserIdentity\Application\Specification\IsExistingEmailSpecification;

class LoginValidator
{
    private TranslatorInterface $translator;
    private IsExistingEmailSpecification $isExistingEmailSpecification;
    private AreValidCredentialsSpecification $areValidCredentialsSpecification;
    private RequestParamValidator $paramValidator;

    public function __construct(
        TranslatorInterface $translator,
        IsExistingEmailSpecification $isExistingEmailSpecification,
        RequestParamValidator $paramValidator,
        AreValidCredentialsSpecification $areValidCredentialsSpecification
    ) {
        $this->translator = $translator;
        $this->isExistingEmailSpecification = $isExistingEmailSpecification;
        $this->paramValidator = $paramValidator;
        $this->areValidCredentialsSpecification = $areValidCredentialsSpecification;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function validate(array $data): ValidationResult
    {
        $errorMap = array_merge(
        // this order is important due to the messages for non existing mail and password matching
            $this->validatePassword($data),
            $this->validateEmail($data),
        );

        return new ValidationResult($errorMap);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, string>
     */
    private function validatePassword(array $data): array
    {
        if (
            !empty($this->paramValidator->validateRequired($data, 'email'))
            ||
            !empty($this->paramValidator->validateString($data, 'email'))
        ) {
            return []; // if no email is sent, don't validate password
        }

        $requiredValidation = $this->paramValidator->validateRequired($data, 'password');
        if (!empty($requiredValidation)) {
            return $requiredValidation;
        }

        $stringValidation = $this->paramValidator->validateString($data, 'password');
        if (!empty($stringValidation)) {
            return $stringValidation;
        }

        $email = $data['email'];
        $password = $data['password'];

        if (!$this->areValidCredentialsSpecification->satisfiedBy($email, $password)) {
            return ['email' => $this->translator->trans('Invalid email or password provided')];
        }

        return [];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, string>
     */
    private function validateEmail(array $data): array
    {
        $requiredValidation = $this->paramValidator->validateRequired($data, 'email');
        if (!empty($requiredValidation)) {
            return $requiredValidation;
        }
        $stringValidation = $this->paramValidator->validateString($data, 'email');
        if (!empty($stringValidation)) {
            return $stringValidation;
        }

        $email = $data['email'];

        if (!$this->isExistingEmailSpecification->satisfiedBy($email)) {
            return ['email' => $this->translator->trans('Invalid email or password provided')];
        }

        return [];
    }
}
