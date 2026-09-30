<?php

declare(strict_types=1);

namespace Exonet\Api\Exceptions;

class ValidationException extends ExonetApiException
{
    /**
     * @var array<string, array<int, string|null>> The detailed error codes of the failed validations, keyed by field.
     */
    protected $failedValidationCodes = [];

    /**
     * Add a validation error to the variable details.
     *
     * @param string|null $field       The name of the field.
     * @param string      $description The returned error description.
     * @param string|null $code        The detailed error code of the failed validation.
     */
    public function setFailedValidation(?string $field, string $description, ?string $code = null): void
    {
        $this->variables[$field ?? 'generic'][] = $description;
        $this->failedValidationCodes[$field ?? 'generic'][] = $code;
    }

    /**
     * Get all failed validations.
     *
     * @return array The failed validation details.
     */
    public function getFailedValidations(): array
    {
        return $this->variables;
    }

    /**
     * Get the detailed error codes of all failed validations, in the same order as getFailedValidations().
     *
     * @return array<string, array<int, string|null>> The detailed error codes, keyed by field.
     */
    public function getFailedValidationCodes(): array
    {
        return $this->failedValidationCodes;
    }
}
