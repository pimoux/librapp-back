<?php

namespace App\Factory;

use App\Entity\Book;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Book>
 *
 * @method static Book|Proxy createOne(array $attributes = [])
 * @method static Book[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Book|Proxy find(object|array|mixed $criteria)
 * @method static Book|Proxy findOrCreate(array $attributes)
 * @method static Book|Proxy first(string $sortedField = 'id')
 * @method static Book|Proxy last(string $sortedField = 'id')
 * @method static Book|Proxy random(array $attributes = [])
 * @method static Book|Proxy randomOrCreate(array $attributes = [])
 * @method static Book[]|Proxy[] all()
 * @method static Book[]|Proxy[] findBy(array $attributes)
 * @method static Book[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Book[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static BookRepository|RepositoryProxy repository()
 * @method Book|Proxy create(array|callable $attributes = [])
 */
final class BookFactory extends ModelFactory
{

    private AuthorRepository $authorRepository;

    public function __construct(AuthorRepository $authorRepository)
    {
        parent::__construct();
        $this->authorRepository = $authorRepository;
    }

    protected function getDefaults(): array
    {
        $authors = $this->authorRepository->findAll();
        return [
            'title' => self::faker()->unique()->jobTitle(),
            'nbPages' => self::faker()->randomNumber(3, true),
            'prix' => self::faker()->randomFloat(2, 6, 50),
            'author' => $authors[self::faker()->numberBetween(0, count($authors) - 1)]
        ];
    }

    protected function initialize(): self
    {
        return $this;
    }

    protected static function getClass(): string
    {
        return Book::class;
    }
}
