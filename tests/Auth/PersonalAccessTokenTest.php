<?php

namespace Exonet\Api\Auth;

use PHPUnit\Framework\TestCase;

class PersonalAccessTokenTest extends TestCase
{
    public function testSetTokenViaConstructor(): void
    {
        $token = 'test_token';

        $authClass = new PersonalAccessToken($token);

        $this->assertSame($token, $authClass->getToken());
    }

    public function testSetTokenViaSetter(): void
    {
        $token = 'test_token';

        $authClass = new PersonalAccessToken();
        $authClass->setToken($token);

        $this->assertSame($token, $authClass->getToken());
    }
}
