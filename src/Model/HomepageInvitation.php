<?php

namespace App\Model;

use App\Validator\Constraints as EPLAssert;
use Symfony\Component\Validator\Constraints as Assert;

class HomepageInvitation
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=100)
     */
    public ?string $firstName = '';

    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public ?string $email = '';

    /**
     * @Assert\Length(max=5)
     * @EPLAssert\ZipCode()
     */
    public ?string $zipCode = '';
}
