<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Author;
use App\Tests\Manager\RequestManager;

class BookTest extends ApiTestCase
{
    const BOOK_DATA = ["title" => "50 nuances d'algos", "nbPages" => 276, "prix" => 19.99, "author" => "/api/authors/1"];
    const INVALID_BOOK_DATA = ["title" => "50 nuances d'algos", "nbPages" => 276, "prix" => 19.99, "author" => 1];

    public function testAdminGetBooks()
    {
        $response = RequestManager::getRequest('/api/books');
    
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/contexts/Book',
            '@id' => '/api/books',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 30
        ]);
        $this->assertCount(30, $response->toArray()['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Author::class);
    }

    public function testAnonymousGetBooks()
    {
        RequestManager::getRequest('/api/books', false);

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => "JWT Token not found"
        ]);
    }

    public function testAdminCreateBook()
    {
        $response = RequestManager::postRequest('/api/books', self::BOOK_DATA);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            "@context" => "/api/contexts/Book",
            "@type" => "Book",
            "title" => "50 nuances d'algos",
            "nbPages" => 276,
            "prix" => 19.99,
            "author" => [
                "@id" => '/api/authors/1',
                "@type" => 'Author'
            ],
            "fileUrl" => null
        ]);
        $this->assertMatchesRegularExpression('~^/api/authors/\d+$~', $response->toArray()['author']['@id']);
        $this->assertMatchesRegularExpression('~^/api/books/\d+$~', $response->toArray()['@id']);
        $this->assertMatchesResourceItemJsonSchema(Author::class);
    }

    public function testAdminCreateInvalidBook()
    {
        RequestManager::postRequest('/api/books', self::INVALID_BOOK_DATA);

        $this->assertResponseStatusCodeSame(400);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            "@context" => "/api/contexts/Error",
            "@type" => "hydra:Error",
            "hydra:title" => "An error occurred",
            "hydra:description" => "Expected IRI or nested document for attribute \"author\", \"integer\" given."
        ]);
    }

    public function testAnonymousCreateBook()
    {
        RequestManager::postRequest('/api/books', self::BOOK_DATA, false);

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => "JWT Token not found"
        ]);
    }
}
