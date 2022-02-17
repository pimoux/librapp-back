<?php

namespace App\Entity;

use ApiPlatform\Core\Action\NotFoundAction;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\CreateBookWithCoverPageController;
use App\Repository\BookRepository;
use DateTime;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ApiResource(
    collectionOperations: [
        "GET" => [
            "normalization_context" => [
                "groups" => ["read:book:collection"]
            ]
        ],
        "POST" => [
            "denormalization_context" => [
                "groups" => ["write:book"]
            ],
            "normalization_context" => [
                "groups" => ["read:book:collection"]
            ]
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
        'DELETE',
        'image' => [
            'method' => 'POST',
            'deserialize' => false,
            'path' => '/books/{id}/image',
            "requirements" => ["id" => "\d+"],
            'controller' => CreateBookWithCoverPageController::class,
            "normalization_context" => [
                "groups" => ["read:book:collection"]
            ],
            'openapi_context' => [
                'requestBody' => [
                    'content' => [
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'file' => [
                                        'type' => 'string',
                                        'format' => 'binary',
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ],
    attributes: [
        'order' => [
            'title' => 'ASC'
        ]
    ]
)]
/**
 * @Vich\Uploadable
 */
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['read:book:collection', "read:author:books"])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['read:book:collection', "read:author:books", "write:book"])]
    private $title;

    #[ORM\Column(type: 'integer')]
    #[Groups(['read:book:collection', "write:book"])]
    private $nbPages;

    #[ORM\Column(type: 'float')]
    #[Groups(['read:book:collection', "write:book"])]
    private $prix;

    #[ORM\ManyToOne(targetEntity: Author::class, inversedBy: 'books')]
    #[Groups(['read:book:collection', "write:book"])]
    private $author;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['read:book:collection'])]
    private $createdAt;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['read:book:collection'])]
    private $updatedAt;

    /**
     * @var File|null
     * @Vich\UploadableField(mapping="cover_page", fileNameProperty="filePath")
     */
    private $file;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $filePath;

    /**
     * @var string|null
     */
    #[Groups(['read:book:collection'])]
    private $fileUrl;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getNbPages(): ?int
    {
        return $this->nbPages;
    }

    public function setNbPages(int $nbPages): self
    {
        $this->nbPages = $nbPages;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setAuthor(?Author $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;
        if ($file !== null) {
            $this->setUpdatedAt(new DateTime());
        }

        return $this;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(?string $filePath): self
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function getFileUrl(): ?string
    {
        return $this->fileUrl;
    }

    public function setFileUrl(?string $fileUrl): self
    {
        $this->fileUrl = $fileUrl;

        return $this;
    }
}
