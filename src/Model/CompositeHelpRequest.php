<?php

namespace App\Model;

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
}
