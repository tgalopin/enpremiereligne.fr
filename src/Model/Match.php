<?php

namespace App\Model;

use App\Entity\Helper;
use App\Entity\HelpRequest;

class Match
{
    private HelpRequest $request;
    private Helper $helper;
    private int $score;

    public function __construct(HelpRequest $request, Helper $helper, int $score)
    {
        $this->request = $request;
        $this->helper = $helper;
        $this->score = $score;
    }

    public function getRequest(): HelpRequest
    {
        return $this->request;
    }

    public function getHelper(): Helper
    {
        return $this->helper;
    }

    public function getScore(): int
    {
        return $this->score;
    }
}
