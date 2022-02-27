<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Author;

class BookTest extends ApiTestCase
{
    const BOOK_DATA = ["title" => "50 nuances d'algos", "nbPages" => 276, "prix" => 19.99, "author" => "/api/authors/1"];

    public function testAdminGetBooks()
    {
        $login = AuthenticationTest::login('123456');
        $token = $login->toArray()['token'];

        $response = self::createClient()->request('GET', '/api/books', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'bearer ' . $token
            ],
        ]);
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
        self::createClient()->request('GET', '/api/books', [
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

    public function testAdminCreateBook()
    {
        $login = AuthenticationTest::login('123456');
        $token = $login->toArray()['token'];

        $response = self::createClient()->request('POST', '/api/books', [
            'json' => self::BOOK_DATA,
            'headers' => [
                'Content-Type' => 'application/ld+json',
                'Authorization' => 'bearer ' . $token
            ],
        ]);

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

    public function testAnonymousCreateBook()
    {
        self::createClient()->request('POST', '/api/books', [
            'json' => self::BOOK_DATA,
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
}
