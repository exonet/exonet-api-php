<?php

declare(strict_types=1);

namespace Exonet\Api\Auth;

/**
 * Use a personal access token to authenticate requests.
 */
class PersonalAccessToken extends AbstractAuth
{
    /**
     * PersonalAccessToken constructor.
     *
     * @param null|string $accessToken The access token to use.
     */
    public function __construct(?string $accessToken = null)
    {
        if ($accessToken) {
            $this->setToken($accessToken);
        }
    }
}
