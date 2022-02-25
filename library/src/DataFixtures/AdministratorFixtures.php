<?php

namespace App\DataFixtures;

use App\Factory\AdministratorFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AdministratorFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        AdministratorFactory::createOne();
    }
}
