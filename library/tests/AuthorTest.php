<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Author;

class AuthorTest extends ApiTestCase
{
    const AUTHOR_DATA = ["firstname" => "Jean", "lastname" => "Pierre", "datns" => "2022-01-07T23:30:39+00:00", "location" => "NY"];
    const INVALID_AUTHOR_DATA = ["firstname" => "Jean", "lastname" => "Pierre", "datns" => 12, "location" => "NY"];

    public function testAdminGetAuthors()
    {
        $login = AuthenticationTest::login('123456');
        $token = $login->toArray()['token'];

        $response = self::createClient()->request('GET', '/api/authors', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'bearer ' . $token
            ],
        ]);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/contexts/Author',
            '@id' => '/api/authors',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 10
        ]);
        $this->assertCount(10, $response->toArray()['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Author::class);
    }

    public function testAnonymousGetAuthors()
    {
        self::createClient()->request('GET', '/api/authors', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => "JWT Token not found"
        ]);
    }

    public function testAdminCreateAuthor()
    {
        $login = AuthenticationTest::login('123456');
        $token = $login->toArray()['token'];

        $response = self::createClient()->request('POST', '/api/authors', [
            'json' => self::AUTHOR_DATA,
            'headers' => [
                'Content-Type' => 'application/ld+json',
                'Authorization' => 'bearer ' . $token
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            "@context" => "/api/contexts/Author",
            "@type" => "Author",
            "firstname" => "Jean",
            "lastname" => "Pierre",
            "datns" => "2022-01-07T23:30:39+00:00",
            "location" => "NY"
        ]);
        $this->assertMatchesRegularExpression('~^/api/authors/\d+$~', $response->toArray()['@id']);
        $this->assertMatchesResourceItemJsonSchema(Author::class);
    }

    public function testAdminCreateInvalidAuthor()
    {
        $login = AuthenticationTest::login('123456');
        $token = $login->toArray()['token'];

        self::createClient()->request('POST', '/api/authors', [
            'json' => self::INVALID_AUTHOR_DATA,
            'headers' => [
                'Content-Type' => 'application/ld+json',
                'Authorization' => 'bearer ' . $token
            ],
        ]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            "@context" => "/api/contexts/Error",
            "@type" => "hydra:Error",
            "hydra:title" => "An error occurred",
            "hydra:description" => "Failed to parse time string (12) at position 0 (1): Unexpected character"
        ]);
    }

    public function testAnonymousCreateAuthor()
    {
        self::createClient()->request('POST', '/api/authors', [
            'json' => self::AUTHOR_DATA,
            'headers' => [
                'Content-Type' => 'application/ld+json'
            ],
        ]);

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => "JWT Token not found"
        ]);
    }

    public function testAdminGetAuthorBooks()
    {
        $login = AuthenticationTest::login('123456');
        $token = $login->toArray()['token'];

        $response = self::createClient()->request('GET', '/api/authors/1/books', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'bearer ' . $token
            ],
        ]);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/contexts/Author',
            '@id' => '/api/authors',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                ['@type' => 'Book']
            ]
        ]);
        $this->assertMatchesResourceCollectionJsonSchema(Author::class);
    }

    public function testAnonymousGetAuthorBooks()
    {
        self::createClient()->request('GET', '/api/authors/1/books', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => "JWT Token not found"
        ]);
    }
}
