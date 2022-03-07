<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class 
 * @package App\Controller
 */
class GetAuthorBooksController extends AbstractController {

    public function __invoke(Author $author, AuthorRepository $authorRepository)
    {
        return $authorRepository->findAuthorBooks($author->getId());
    }
}