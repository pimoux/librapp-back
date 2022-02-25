<?php

namespace App\Factory;

use App\Entity\Administrator;
use App\Repository\AdministratorRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Administrator>
 *
 * @method static Administrator|Proxy createOne(array $attributes = [])
 * @method static Administrator[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Administrator|Proxy find(object|array|mixed $criteria)
 * @method static Administrator|Proxy findOrCreate(array $attributes)
 * @method static Administrator|Proxy first(string $sortedField = 'id')
 * @method static Administrator|Proxy last(string $sortedField = 'id')
 * @method static Administrator|Proxy random(array $attributes = [])
 * @method static Administrator|Proxy randomOrCreate(array $attributes = [])
 * @method static Administrator[]|Proxy[] all()
 * @method static Administrator[]|Proxy[] findBy(array $attributes)
 * @method static Administrator[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Administrator[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static AdministratorRepository|RepositoryProxy repository()
 * @method Administrator|Proxy create(array|callable $attributes = [])
 */
final class AdministratorFactory extends ModelFactory
{

    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        parent::__construct();
        $this->hasher = $hasher;
    }

    protected function getDefaults(): array
    {
        return [
            'email' => "johndoe@gmail.com",
            'roles' => ['ROLE_ADMIN'],
            'password' => $this->hasher->hashPassword(new Administrator(), '123456'),
            'firstname' => "john",
            'lastname' => "doe",
        ];
    }

    protected function initialize(): self
    {
        return $this;
    }

    protected static function getClass(): string
    {
        return Administrator::class;
    }
}
