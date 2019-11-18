<?php

declare(strict_types=1);

namespace Exonet\Api\Exceptions;

class ValidationException extends ExonetApiException
{
    /**
     * Add a validation error to the variable details.
     *
     * @param string|null $field       The name of the field.
     * @param string      $description The returned error description.
     */
    public function setFailedValidation(?string $field, string $description) : void
    {
        $this->variables[$field ?? 'generic'][] = $description;
    }

    /**
     * Get all failed validations.
     *
     * @return array The failed validation details.
     */
    public function getFailedValidations() : array
    {
        return $this->variables;
    }
}
