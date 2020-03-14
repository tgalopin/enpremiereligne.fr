<?php

namespace App\Model;

class CompositeHelpRequestDetail
{
    /**
     * @Assert\NotBlank()
     * @Assert\Choice({"babysit", "groceries"})
     */
    public ?string $helpType;

    /**
     * @ORM\Column(length=10, nullable=true)
     *
     * @Assert\Choice(callback={"App\Entity\HelpRequest", "getAgeRanges"})
     */
    public ?string $childAgeRange;
}
