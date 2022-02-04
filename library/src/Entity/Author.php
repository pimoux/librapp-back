<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\GetAuthorBooksController;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\AuthorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AuthorRepository::class)]
#[ApiResource(
    collectionOperations: [
        "GET" => [
            "normalization_context" => [
                "groups" => ["read:author:collection"]
            ]
        ],
        "POST" => [
            "denormalization_context" => [
                "groups" => ["write:author"]
            ],
        ]
        ],
    itemOperations: [
        "GET" => [
            'controller' => NotFoundAction::class,
            'read' => false,
            'output' => false,
            'openapi_context' => [
                "summary" => "hidden"
            ]
        ], 
        "book" => [
            "pagination_enabled" => false,
            "path" => "authors/{id}/books",
            "requirements" => ["id" => "\d+"],
            "read" => false,
            "controller" => GetAuthorBooksController::class,
            "method" => "GET",
            "normalization_context" => [
                "groups" => ["read:author:books"]
            ]
        ]
    ]
)]
class Author
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["read:author:collection"])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["write:author", "read:book:collection", "read:author:collection"])]
    private $firstname;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["write:author", "read:book:collection", "read:author:collection"])]
    private $lastname;

    #[ORM\Column(type: 'datetime')]
    #[Groups(["write:author", "read:author:collection"])]
    private $datns;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["write:author", "read:author:collection"])]
    private $location;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Book::class)]
    #[Groups(["read:author:books"])]
    private $books;

    public function __construct()
    {
        $this->books = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getDatns(): ?\DateTimeInterface
    {
        return $this->datns;
    }

    public function setDatns(\DateTimeInterface $datns): self
    {
        $this->datns = $datns;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return Collection|Book[]
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBook(Book $book): self
    {
        if (!$this->books->contains($book)) {
            $this->books[] = $book;
            $book->setAuthor($this);
        }

        return $this;
    }

    public function removeBook(Book $book): self
    {
        if ($this->books->removeElement($book)) {
            // set the owning side to null (unless already changed)
            if ($book->getAuthor() === $this) {
                $book->setAuthor(null);
            }
        }

        return $this;
    }
}
