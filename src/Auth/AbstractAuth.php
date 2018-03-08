<?php

declare(strict_types=1);

namespace Exonet\Api\Auth;

/**
 * Every authentication method must extend this authentication class.
 */
abstract class AbstractAuth
{
    /**
     * @var string The access token.
     */
    protected $token;

    /**
     * Get the access token.
     *
     * @return string The access token.
     */
    public function getToken() : string
    {
        return $this->token;
    }

    /**
     * Set the access token.
     *
     * @param string $token The access token.
     */
    public function setToken(string $token) : void
    {
        $this->token = $token;
    }
}
