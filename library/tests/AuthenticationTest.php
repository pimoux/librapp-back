<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Contracts\HttpClient\ResponseInterface;

class AuthenticationTest extends ApiTestCase
{
    public static function login(string $password): ResponseInterface
    {
        return self::createClient()->request('POST', '/api/login_check', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'username' => 'johndoe@gmail.com',
                'password' => $password
            ]
        ]);
    }

    public function testSuccessfulLogin()
    {
        $json = $this->login('123456')->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $json);
    }

    public function testInvalidCredentials()
    {
        $this->login('invalidCredentials');

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => "Invalid credentials."
        ]);
    }

    public function testTokenNotFound()
    {
        self::createClient()->request('GET', '/api/books');

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => "JWT Token not found"
        ]);
    }
}
