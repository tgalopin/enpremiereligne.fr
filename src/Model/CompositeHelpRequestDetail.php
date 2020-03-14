<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class CompositeHelpRequestDetail
{
    /**
     * @Assert\NotBlank()
     * @Assert\Choice({"babysit", "groceries"})
     */
    public ?string $helpType = null;

    /**
     * @Assert\Choice(callback={"App\Entity\HelpRequest", "getAgeRanges"})
     */
    public ?string $childAgeRange = null;

    /**
     * @Assert\Callback()
     */
    public function validate(ExecutionContextInterface $context)
    {
        if ('babysit' === $this->helpType && !$this->childAgeRange) {
            $context->addViolation('Vous devez renseigner l\'âge de votre enfant à garder.');
        }
    }
}
