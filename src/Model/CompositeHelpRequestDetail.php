<?php

namespace App\Model;

use Symfony\Component\Validator\Context\ExecutionContextInterface;

class CompositeHelpRequestDetail
{
    /**
     * @Assert\NotBlank()
     * @Assert\Choice({"babysit", "groceries"})
     */
    public ?string $helpType = null;

    /**
     * @ORM\Column(length=10, nullable=true)
     *
     * @Assert\Choice(callback={"App\Entity\HelpRequest", "getAgeRanges"})
     */
    public ?string $childAgeRange = null;

    /**
     * @Assert\Callback()
     */
    public function validate(ExecutionContextInterface $context)
    {
        dump($this);exit;
    }
}
