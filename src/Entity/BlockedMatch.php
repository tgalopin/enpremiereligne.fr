<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BlockedMatchRepository")
 */
class BlockedMatch
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint", options={"unsigned": true})
     */
    private ?int $id;

    /**
     * @ORM\Column(type="uuid")
     */
    public UuidInterface $ownerUuid;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Helper", inversedBy="blockedMatches")
     * @ORM\JoinColumn(nullable=false)
     */
    public Helper $helper;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTime $createdAt;

    public function __construct(UuidInterface $ownerUuid, Helper $helper)
    {
        $this->ownerUuid = $ownerUuid;
        $this->helper = $helper;
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwner(): string
    {
        return $this->ownerUuid->toString();
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }
}
