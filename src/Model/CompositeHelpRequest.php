<?php

namespace App\Model;

use App\Entity\HelpRequest;
use App\Validator\Constraints as EPLAssert;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class CompositeHelpRequest
{
    /**
     * @Assert\NotBlank(message="name-first.required")
     * @Assert\Length(max=100)
     */
    public ?string $firstName = '';

    /**
     * @Assert\NotBlank(message="name-last.required")
     * @Assert\Length(max=100)
     */
    public ?string $lastName = '';

    /**
     * @Assert\NotBlank(message="email.required")
     * @Assert\Email()
     * @Assert\Length(max=200)
     */
    public ?string $email = '';

    /**
     * @Assert\NotBlank(message="postcode.required")
     * @Assert\Length(max=5)
     * @EPLAssert\ZipCode()
     */
    public ?string $zipCode = '';

    /**
     * @Assert\NotBlank()
     * @Assert\Choice({"health", "emergency", "care", "food", "drugs", "energy", "transports", "other"})
     */
    public ?string $jobType = '';

    /**
     * @var array|CompositeHelpRequestDetail[]
     *
     * @Assert\Valid()
     */
    public array $details = [];

    /**
     * @Assert\Callback()
     */
    public function validate(ExecutionContextInterface $context)
    {
        if (!$this->details) {
            $context->addViolation('needs.at-least-one');
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
