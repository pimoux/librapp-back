<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Author;
use App\Tests\Manager\RequestManager;

class AuthorTest extends ApiTestCase
{
    const AUTHOR_DATA = ["firstname" => "Jean", "lastname" => "Pierre", "datns" => "2022-01-07T23:30:39+00:00", "location" => "NY"];
    const INVALID_AUTHOR_DATA = ["firstname" => "Jean", "lastname" => "Pierre", "datns" => 12, "location" => "NY"];

    public function testAdminGetAuthors()
    {
        $response = RequestManager::getRequest('/api/authors');

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
        RequestManager::getRequest('/api/authors', false);

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => "JWT Token not found"
        ]);
    }

    public function testAdminCreateAuthor()
    {
        $response = RequestManager::postRequest('/api/authors', self::AUTHOR_DATA);

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
        RequestManager::postRequest('/api/authors', self::INVALID_AUTHOR_DATA);

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
        RequestManager::postRequest('/api/authors', self::AUTHOR_DATA, false);

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => "JWT Token not found"
        ]);
    }

    public function testAdminGetAuthorBooks()
    {
        RequestManager::getRequest('/api/authors/1/books');

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
        RequestManager::getRequest('/api/authors/1/books', false);

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => "JWT Token not found"
        ]);
    }
}
