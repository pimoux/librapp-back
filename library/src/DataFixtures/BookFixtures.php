<?php

namespace App\DataFixtures;

use App\Factory\BookFactory;
use App\Entity\Book;
use App\Repository\AuthorRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BookFixtures extends Fixture
{
    public function __construct(private AuthorRepository $authorRepository)
    {
    }
    public function load(ObjectManager $manager): void
    {
        $book = new Book();
        $book->setTitle('Le tour du monde en 80 frameworks');
        $book->setNbPages(456);
        $book->setPrix(19.99);
        $book->setAuthor($this->authorRepository->find(1));
        $manager->persist($book);
        $manager->flush();
        BookFactory::createMany(29);
    }
}
