<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HelperRepository")
 * @ORM\Table(name="helpers")
 */
class Helper
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint", options={"unsigned": true})
     */
    private ?int $id;

    /**
     * @ORM\Column(type="uuid", unique=true)
     */
    private ?UuidInterface $uuid;

    /**
     * @ORM\Column(length=100)
     *
     * @Assert\NotBlank()
     * @Assert\Length(max=100)
     */
    public ?string $firstName;

    /**
     * @ORM\Column(length=100)
     *
     * @Assert\NotBlank()
     * @Assert\Length(max=100)
     */
    public ?string $lastName;

    /**
     * @ORM\Column(length=200)
     *
     * @Assert\NotBlank()
     * @Assert\Email()
     * @Assert\Length(max=200)
     */
    public ?string $email;

    /**
     * @ORM\Column(length=10)
     *
     * @Assert\NotBlank()
     * @Assert\Length(max=5)
     */
    public ?string $zipCode;

    /**
     * @ORM\Column(type="boolean")
     */
    public ?bool $haveChildren = false;

    /**
     * @ORM\Column(type="boolean")
     */
    public ?bool $canBabysit = false;

    /**
     * @ORM\Column(type="smallint")
     *
     * @Assert\GreaterThanOrEqual(0)
     * @Assert\LessThanOrEqual(4)
     */
    public ?int $babysitMaxChildren = 0;

    /**
     * @ORM\Column(type="simple_array")
     */
    public array $babysitAgeRanges = [];

    /**
     * @ORM\Column(type="boolean")
     */
    public ?bool $canBuyGroceries = false;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTime $createdAt;

    public function __construct()
    {
        $this->uuid = Uuid::uuid4();
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?UuidInterface
    {
        return $this->uuid;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }
}
