<?php

declare(strict_types=1);

namespace Exonet\Api\Exceptions;

class ExonetApiException extends \Exception
{
    /**
     * @var string|null The detailed error code.
     */
    protected $detailCode;

    /**
     * @var array Array with detailed information if provided by the API.
     */
    protected $variables = [];

    /**
     * ExonetApiException constructor.
     *
     * @param string          $message    (Optional) The exception message to throw.
     * @param int             $code       (Optional) The exception code.
     * @param \Throwable|null $previous   (Optional) The previous throwable used for the exception chaining.
     * @param string|null     $detailCode (Optional) The detailed error code.
     * @param array           $variables  (Optional) Array with detailed information if provided by the API.
     */
    public function __construct($message = '', $code = 0, ?\Throwable $previous = null, $detailCode = null, $variables = [])
    {
        parent::__construct($message, $code, $previous);
        $this->detailCode = $detailCode;
        $this->variables = $variables;
    }

    /**
     * Get the detailed error code.
     *
     * @return string|null The detailed error code.
     */
    public function getDetailCode(): ?string
    {
        return $this->detailCode;
    }

    /**
     * Get the array with detailed information if provided by the API.
     *
     * @return array Array with detailed information if provided by the API.
     */
    public function getVariables(): array
    {
        return $this->variables;
    }
}
