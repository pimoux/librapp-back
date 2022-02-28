<?php

namespace App\Tests\Manager;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Contracts\HttpClient\ResponseInterface;

class RequestManager extends ApiTestCase
{
    public static function getRequest(string $endpoint, bool $isLoggedIn = true): ResponseInterface
    {
        $headers = [];
        if ($isLoggedIn) {
            $login = RequestManager::login();
            $token = $login->toArray()['token'];
            $headers = [
                'Content-Type' => 'application/json',
                'Authorization' => 'bearer ' . $token
            ];
        } else {
            $headers = ['Content-Type' => 'application/json'];
        }

        return self::createClient()->request('GET', $endpoint, [
            'headers' => $headers
        ]);
    }

    public static function login(): ResponseInterface
    {
        return self::createClient()->request('POST', '/api/login_check', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'username' => 'johndoe@gmail.com',
                'password' => '123456'
            ]
        ]);
    }

    public static function invalidLogin(): ResponseInterface
    {
        return self::createClient()->request('POST', '/api/login_check', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'username' => 'johndoe@gmail.com',
                'password' => 'invalidCredentials'
            ]
        ]);
    }

    public static function postRequest(string $endpoint, array $body, bool $isLoggedIn = true): ResponseInterface
    {
        $headers = [];
        if ($isLoggedIn) {
            $login = RequestManager::login();
            $token = $login->toArray()['token'];
            $headers = [
                'Content-Type' => 'application/ld+json',
                'Authorization' => 'bearer ' . $token
            ];
        } else {
            $headers = ['Content-Type' => 'application/ld+json'];
        }

        return self::createClient()->request('POST', $endpoint, [
            'json' => $body,
            'headers' => $headers
        ]);
    }

    public static function postFile(string $endpoint, UploadedFile $file, bool $isLoggedIn = true)
    {
        $headers = [];
        if ($isLoggedIn) {
            $login = RequestManager::login();
            $token = $login->toArray()['token'];
            $headers = [
                'Content-Type' => 'application/ld+json',
                'Authorization' => 'bearer ' . $token
            ];
        } else {
            $headers = ['Content-Type' => 'application/ld+json'];
        }

        return self::createClient()->request('POST', $endpoint, [
            'headers' => $headers,
            'extra' => [
                'files' => [
                    'file' => $file
                ]
            ]
        ]);
    }
}
