<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PostCoverPageTest extends ApiTestCase
{
    // public function testAdminPublishCoverPage()
    // {
    //     $file = new UploadedFile(__DIR__ . '/images/profile.jpeg', 'profile.jpeg', 'image/jpeg');
    //     $login = AuthenticationTest::login('123456');
    //     $token = $login->toArray()['token'];

    //     $response = self::createClient()->request('POST', '/api/books/1/image', [
    //         'headers' => [
    //             'Content-Type' => 'multipart/form-data',
    //             'Authorization' => 'bearer ' . $token
    //         ],
    //         'extra' => [
    //             'files' => [
    //                 'file' => $file
    //             ]
    //         ]
    //     ]);

    //     $this->assertResponseStatusCodeSame(201);
    //     $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    //     $this->assertJsonContains([
    //         "@context" => "/api/contexts/Book",
    //         "@type" => "Book",
    //         "author" => [
    //             "@type" => "Author"
    //         ]
    //     ]);
    //     $this->assertMatchesRegularExpression('~^/api/books/\d+$~', $response->toArray()['@id']);
    // }

    public function testAdminPublishInvalidCoverPage()
    {
        $pdfFile = new UploadedFile(__DIR__ . '/images/invalidFile.pdf', 'invalidFile.pdf', 'application/pdf');
        $login = AuthenticationTest::login('123456');
        $token = $login->toArray()['token'];

        $response = self::createClient()->request('POST', '/api/books/2/image', [
            'headers' => [
                'Content-Type' => 'multipart/form-data',
                'Authorization' => 'bearer ' . $token
            ],
            'extra' => [
                'files' => [
                    'file' => $pdfFile
                ]
            ]
        ]);

        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            "@context" => "/api/contexts/ConstraintViolationList",
            "@type" => "ConstraintViolationList",
            "hydra:title" => "An error occurred",
            "hydra:description" => "file: Please upload an image with a valid format"
        ]);
    }

    public function testAdminPublishCoverPageForNotFoundBook()
    {
        $file = new UploadedFile(__DIR__ . '/images/profileNotFound.jpeg', 'profileNotFound.jpeg', 'image/jpeg');
        $login = AuthenticationTest::login('123456');
        $token = $login->toArray()['token'];

        $response = self::createClient()->request('POST', '/api/books/1000/image', [
            'headers' => [
                'Content-Type' => 'multipart/form-data',
                'Authorization' => 'bearer ' . $token
            ],
            'extra' => [
                'files' => [
                    'file' => $file
                ]
            ]
        ]);

        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            "@context" => "/api/contexts/Error",
            "@type" => "hydra:Error",
            "hydra:title" => "An error occurred",
            "hydra:description" => "Not Found"
        ]);
    }

    public function testAnonymousPublishCoverPage()
    {
        $file = new UploadedFile(__DIR__ . '/images/profileAnonymous.jpeg', 'profileAnonymous.jpeg', 'image/jpeg');
        self::createClient()->request('POST', '/api/books/3/image', [
            'headers' => [
                'Content-Type' => 'multipart/form-data'
            ],
            'extra' => [
                'files' => [
                    'file' => $file
                ]
            ]
        ]);

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => "JWT Token not found"
        ]);
    }
}
