<?php

namespace App\Model;

use App\Entity\HelpRequest;

class MatchedNeeds
{
    /**
     * @var HelpRequest[]
     */
    private array $needs;

    private ?MatchedNeed $groceries;
    private ?MatchedNeed $babysit;
    private int $score;

    public function __construct(array $needs, ?MatchedNeed $groceries = null, ?MatchedNeed $babysit = null, int $score = 0)
    {
        $this->needs = $needs;
        $this->groceries = $groceries;
        $this->babysit = $babysit;
        $this->score = $score;
    }

    public function getRequester(): HelpRequest
    {
        return $this->needs[0];
    }

    /**
     * @return HelpRequest[]
     */
    public function getNeeds(): array
    {
        return $this->needs;
    }

    public function getGroceriesNeed(): ?HelpRequest
    {
        foreach ($this->needs as $need) {
            if ($need->helpType === HelpRequest::TYPE_GROCERIES) {
                return $need;
            }
        }

        return null;
    }

    /**
     * @return HelpRequest[]
     */
    public function getBabysitNeeds(): array
    {
        $needs = [];
        foreach ($this->needs as $need) {
            if ($need->helpType === HelpRequest::TYPE_BABYSIT) {
                $needs[] = $need;
            }
        }

        return $needs;
    }

    public function getBabysitAgesList(): string
    {
        $ages = [];
        foreach ($this->needs as $need) {
            if ($need->helpType === HelpRequest::TYPE_BABYSIT) {
                $ages[] = $need->childAgeRange.' ans';
            }
        }

        if (count($ages) <= 1) {
            return implode('', $ages);
        }

        $lastAge = array_shift($ages);

        return implode(', ', $ages). ' et '.$lastAge;
    }

    public function getGroceries(): ?MatchedNeed
    {
        return $this->groceries;
    }

    public function getBabysit(): ?MatchedNeed
    {
        return $this->babysit;
    }

    public function getScore(): int
    {
        return $this->score;
    }
}
