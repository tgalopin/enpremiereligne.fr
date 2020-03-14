<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HelpRequestRepository")
 * @ORM\Table(name="help_requests")
 */
class HelpRequest
{
    public const TYPE_BABYSIT = 'babysit';
    public const TYPE_GROCERIES = 'groceries';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint", options={"unsigned": true})
     */
    private ?int $id;

    /**
     * @ORM\Column(length=20)
     */
    public ?string $helpType;

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
     * @ORM\Column(type="smallint", nullable=true)
     *
     * @Assert\GreaterThanOrEqual(0)
     * @Assert\LessThanOrEqual(18)
     */
    public ?int $childAge = 0;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTime $createdAt;
}
