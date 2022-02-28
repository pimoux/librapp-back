<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\Manager\RequestManager;

class AuthenticationTest extends ApiTestCase
{
    public function testSuccessfulLogin()
    {
        $json = RequestManager::login()->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $json);
    }

    public function testInvalidCredentials()
    {
        RequestManager::invalidLogin();

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => "Invalid credentials."
        ]);
    }
}
