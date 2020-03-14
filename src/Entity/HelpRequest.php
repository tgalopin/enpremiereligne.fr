<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HelpRequestRepository")
 * @ORM\Table(name="help_requests", indexes={
 *     @ORM\Index(name="help_requests_owner_idx", columns={"owner_uuid"})
 * })
 */
class HelpRequest
{
    public const TYPE_BABYSIT = 'babysit';
    public const TYPE_GROCERIES = 'groceries';

    public const AGE_RANGE_01 = '0-1';
    public const AGE_RANGE_12 = '1-2';
    public const AGE_RANGE_35 = '3-5';
    public const AGE_RANGE_69 = '6-9';
    public const AGE_RANGE_1012 = '10-12';
    public const AGE_RANGE_13 = '13-';

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
     * @ORM\Column(type="uuid")
     */
    public ?UuidInterface $ownerUuid;

    /**
     * @ORM\Column(length=20)
     *
     * @Assert\NotBlank()
     * @Assert\Choice({"babysit", "groceries"})
     */
    public ?string $helpType;

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
     * @ORM\Column(length=50)
     *
     * @Assert\NotBlank()
     * @Assert\Choice({"health", "food", "drugs", "other"})
     */
    public ?string $jobType;

    /**
     * @ORM\Column(length=10, nullable=true)
     *
     * @Assert\Choice(callback="getAgeRanges")
     */
    public ?string $childAgeRange;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?bool $finished = false;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTime $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Helper", inversedBy="requests")
     */
    public ?Helper $matchedWith;

    public function __construct()
    {
        $this->uuid = Uuid::uuid4();
        $this->createdAt = new \DateTime();
    }

    public static function getAgeRanges()
    {
        return [
            self::AGE_RANGE_01,
            self::AGE_RANGE_12,
            self::AGE_RANGE_35,
            self::AGE_RANGE_69,
            self::AGE_RANGE_1012,
            self::AGE_RANGE_13,
        ];
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
