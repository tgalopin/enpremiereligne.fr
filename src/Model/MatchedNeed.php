<?php

namespace App\Model;

use App\Entity\HelpRequest;

class MatchedNeed
{
    private HelpRequest $request;

    /**
     * @var Match[]
     */
    private array $matchedHelpers = [];

    public function __construct(HelpRequest $request, array $matchedHelpers)
    {
        $this->request = $request;
        $this->matchedHelpers = $matchedHelpers;
    }

    public function getRequest(): HelpRequest
    {
        return $this->request;
    }

    /**
     * @return Match[]
     */
    public function getMatchedHelpers(): array
    {
        return $this->matchedHelpers;
    }
}
