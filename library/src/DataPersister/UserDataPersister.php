<?php
declare(strict_types=1);

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class UserDataPersister
 * @package App\DataPersister
 */
class UserDataPersister implements ContextAwareDataPersisterInterface
{

    /**
     * UserDataPersister constructor.
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordHasherInterface $passwordHasher
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ){}

    /**
     * @param $data
     * @param array $context
     * @return bool
     */
    public function supports($data, array $context = []): bool
    {
        return $data instanceof User;
    }

    /**
     * Hash user password when user is created with the api
     * @param User $data
     * @param array $context
     * @return void
     */
    public function persist($data, array $context = [])
    {
        if ($data->getPassword()) {
            $data->setPassword(
                $this->passwordHasher->hashPassword(
                    $data,
                    $data->getPassword()
                )
            );

            $data->eraseCredentials();
        }
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    /**
     * @param $data
     * @param array $context
     */
    public function remove($data, array $context = [])
    {
        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }
}