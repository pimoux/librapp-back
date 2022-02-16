<?php

namespace App\Controller;

use App\Entity\Book;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;

class CreateBookWithCoverPageController
{
    public function __invoke(Request $request, Book $book)
    {
        $book = $request->attributes->get('data');
        if (!($book instanceof Book)) {
            throw new RuntimeException('Livre attendu');
        }
        // $book->setFile($request->files->get('file'));
        // $book->setUpdatedAt(new \DateTime());
        return $book;
    }
}
