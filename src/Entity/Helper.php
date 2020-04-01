<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @Assert\NotBlank(message="name-first.required")
     * @Assert\Length(max=100)
     */
    public ?string $firstName = '';

    /**
     * @ORM\Column(length=100)
     *
     * @Assert\NotBlank(message="name-last.required")
     * @Assert\Length(max=100)
     */
    public ?string $lastName = '';

    /**
     * @ORM\Column(length=200)
     *
     * @Assert\NotBlank(message="email.required")
     * @Assert\Email()
     * @Assert\Length(max=200)
     */
    public ?string $email = '';

    /**
     * @ORM\Column(length=10)
     *
     * @Assert\NotBlank(message="postcode.required")
     * @Assert\Length(max=5)
     * @Assert\Regex("/^[0-9]{5}$/", htmlPattern="^[0-9]{5}$", message="postcode.wrong-length")
     */
    public ?string $zipCode = '';

    /**
     * @ORM\Column(type="smallint")
     *
     * @Assert\NotBlank(message="age.required")
     * @Assert\GreaterThanOrEqual(18)
     * @Assert\LessThanOrEqual(60)
     */
    public ?int $age = null;

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
     * @Assert\GreaterThanOrEqual(1)
     * @Assert\LessThanOrEqual(4, message="babysit.too-many")
     */
    public ?int $babysitMaxChildren = 1;

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     *
     * @Assert\All(@Assert\Choice(callback={"App\Entity\HelpRequest", "getAgeRanges"}))
     */
    public ?array $babysitAgeRanges = [];

    /**
     * @ORM\Column(type="boolean")
     */
    public ?bool $canBuyGroceries = false;

    /**
     * @ORM\Column(type="boolean")
     */
    public ?bool $acceptVulnerable = false;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTime $createdAt;

    /**
     * @var Collection|HelpRequest[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\HelpRequest", mappedBy="matchedWith")
     */
    private Collection $requests;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BlockedMatch", mappedBy="helper", orphanRemoval=true)
     */
    private Collection $blockedMatches;

    public function __construct()
    {
        $this->uuid = Uuid::uuid4();
        $this->createdAt = new \DateTime();
        $this->requests = new ArrayCollection();
        $this->blockedMatches = new ArrayCollection();
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

    public function getIncompleteName(): string
    {
        return $this->firstName.' '.strtoupper($this->lastName[0]).'.';
    }

    /**
     * @return Collection|HelpRequest[]
     */
    public function getRequests(): Collection
    {
        return $this->requests;
    }

    /**
     * @return Collection|BlockedMatch[]
     */
    public function getBlockedMatches(): Collection
    {
        return $this->blockedMatches;
    }
}
