<?php

namespace App\Controller;

use App\Entity\Book;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

final class CreateBookWithCoverPageController extends AbstractController
{
    public function __invoke(Request $request): Book
    {
        $uploadedFile = $request->files->get('file');
        if (!$uploadedFile) {
            throw new BadRequestHttpException('"file" is required');
        }

        $book = $request->attributes->get('data');
        $book->setFile($uploadedFile);

        return $book;
    }
}
