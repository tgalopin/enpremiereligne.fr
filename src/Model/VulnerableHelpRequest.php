<?php

namespace App\Model;

use App\Entity\HelpRequest;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

class VulnerableHelpRequest
{
    /**
     * @Assert\NotBlank(message="Le prénom est requis.")
     * @Assert\Length(max=100)
     */
    public ?string $firstName = '';

    /**
     * @Assert\NotBlank(message="Le nom de famille est requis.")
     * @Assert\Length(max=100)
     */
    public ?string $lastName = '';

    /**
     * @Assert\NotBlank(message="L'adresse e-mail est requise.")
     * @Assert\Email()
     * @Assert\Length(max=200)
     */
    public ?string $email = '';

    /**
     * @Assert\Email()
     * @Assert\Length(max=200)
     */
    public ?string $ccEmail = '';

    /**
     * @Assert\NotBlank(message="Le code postal est requis.")
     * @Assert\Length(max=5)
     * @Assert\Regex("/^[0-9]{5}$/", htmlPattern="^[0-9]{5}$", message="Le code postal doit contenir précisément 5 chiffres.")
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
        $request->zipCode = $this->zipCode;
        $request->jobType = 'vulnerable';
        $request->helpType = HelpRequest::TYPE_GROCERIES;

        return $request;
    }
}
