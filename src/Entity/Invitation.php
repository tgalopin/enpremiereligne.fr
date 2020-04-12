<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InvitationRepository")
 */
class Invitation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(length=64, unique=true)
     */
    private $emailHash;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function __construct(string $emailHash)
    {
        $this->emailHash = $emailHash;
        $this->createdAt = new \DateTime();
    }

    public static function hashEmail(string $email): string
    {
        return md5(strtolower($email));
    }

    public function getId()
    {
        return $this->id;
    }

    public function getEmailHash(): string
    {
        return $this->emailHash;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
}
