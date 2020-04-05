<?php

namespace App\Model;

use App\Entity\HelpRequest;
use App\Validator\Constraints as EPLAssert;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

class VulnerableHelpRequest
{
    /**
     * @Assert\NotBlank(message="name-first-at-risk.required")
     * @Assert\Length(max=100)
     */
    public ?string $firstName = '';

    /**
     * @Assert\NotBlank(message="name-last-at-risk.required")
     * @Assert\Length(max=100)
     */
    public ?string $lastName = '';

    /**
     * @Assert\NotBlank(message="email-at-risk.required")
     * @Assert\Email()
     * @Assert\Length(max=200)
     */
    public ?string $email = '';

    /**
     * @Assert\Length(max=100)
     */
    public ?string $ccFirstName = '';

    /**
     * @Assert\Length(max=100)
     */
    public ?string $ccLastName = '';

    /**
     * @Assert\Email()
     * @Assert\Length(max=200)
     */
    public ?string $ccEmail = '';

    /**
     * @Assert\NotBlank(message="postcode-at-risk.required")
     * @Assert\Length(max=5)
     * @EPLAssert\ZipCode()
     */
    public ?string $zipCode = '';

    public function createStandaloneRequest(UuidInterface $ownerUuid)
    {
        $request = new HelpRequest();
        $request->ownerUuid = $ownerUuid;
        $request->firstName = $this->firstName;
        $request->lastName = $this->lastName;
        $request->email = strtolower($this->email);
        $request->ccEmail = $this->ccEmail ? strtolower($this->ccEmail) : null;
        $request->ccFirstName = $this->ccFirstName;
        $request->ccLastName = $this->ccLastName;
        $request->zipCode = $this->zipCode;
        $request->jobType = 'vulnerable';
        $request->helpType = HelpRequest::TYPE_GROCERIES;

        return $request;
    }
}
