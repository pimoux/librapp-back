<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Author;

class AuthorTest extends ApiTestCase
{
    public function testGetAuthors() {
        $login = AuthenticationTest::login('123456');
        $token = $login->toArray()['token'];

        $response = self::createClient()->request('GET', '/api/authors', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'bearer ' . $token
            ],
        ]);
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
}
