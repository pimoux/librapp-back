<?php

namespace App\Factory;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Author>
 *
 * @method static Author|Proxy createOne(array $attributes = [])
 * @method static Author[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Author|Proxy find(object|array|mixed $criteria)
 * @method static Author|Proxy findOrCreate(array $attributes)
 * @method static Author|Proxy first(string $sortedField = 'id')
 * @method static Author|Proxy last(string $sortedField = 'id')
 * @method static Author|Proxy random(array $attributes = [])
 * @method static Author|Proxy randomOrCreate(array $attributes = [])
 * @method static Author[]|Proxy[] all()
 * @method static Author[]|Proxy[] findBy(array $attributes)
 * @method static Author[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Author[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static AuthorRepository|RepositoryProxy repository()
 * @method Author|Proxy create(array|callable $attributes = [])
 */
final class AuthorFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        return [
            'firstname' => self::faker()->firstName(),
            'lastname' => self::faker()->lastName(),
            'datns' => self::faker()->dateTimeBetween(),
            'location' => self::faker()->city(),
        ];
    }

    protected function initialize(): self
    {
        return $this;
    }

    protected static function getClass(): string
    {
        return Author::class;
    }
}
