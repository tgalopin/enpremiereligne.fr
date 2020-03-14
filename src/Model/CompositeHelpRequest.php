<?php

namespace App\Model;

use App\Entity\HelpRequest;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class CompositeHelpRequest
{
    /**
     * @Assert\NotBlank(message="Votre prénom est requis.")
     * @Assert\Length(max=100)
     */
    public ?string $firstName = '';

    /**
     * @Assert\NotBlank(message="Votre nom de famille est requis.")
     * @Assert\Length(max=100)
     */
    public ?string $lastName = '';

    /**
     * @Assert\NotBlank(message="Votre adresse e-mail est requise.")
     * @Assert\Email()
     * @Assert\Length(max=200)
     */
    public ?string $email = '';

    /**
     * @Assert\NotBlank(message="Votre code postal est requis.")
     * @Assert\Length(max=5)
     * @Assert\Regex("/^[0-9]{5}$/", htmlPattern="^[0-9]{5}$", message="Le code postal doit contenir précisément 5 chiffres.")
     */
    public ?string $zipCode = '';

    /**
     * @Assert\NotBlank()
     * @Assert\Choice({"health", "emergency", "care", "food", "drugs", "energy", "transports", "other"})
     */
    public ?string $jobType = '';

    /**
     * @var array|CompositeHelpRequestDetail[]
     */
    public array $details = [];

    /**
     * @Assert\Callback()
     */
    public function validate(ExecutionContextInterface $context)
    {
        if (!$this->details) {
            $context->addViolation('Vous devez renseigner au moins un besoin pour vous enregistrer.');
        }
    }

    public function createStandaloneRequests(UuidInterface $ownerUuid)
    {
        $requests = [];
        foreach ($this->details as $detail) {
            $request = new HelpRequest();
            $request->ownerUuid = $ownerUuid;
            $request->firstName = $this->firstName;
            $request->lastName = $this->lastName;
            $request->email = strtolower($this->email);
            $request->zipCode = $this->zipCode;
            $request->jobType = $this->jobType;
            $request->helpType = $detail->helpType;
            $request->childAgeRange = $detail->childAgeRange;

            $requests[] = $request;
        }

        return $requests;
    }
}
