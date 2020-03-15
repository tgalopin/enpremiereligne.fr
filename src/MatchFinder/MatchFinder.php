<?php

namespace App\MatchFinder;

use App\Entity\Helper;
use App\Entity\HelpRequest;
use App\Repository\HelperRepository;

class MatchFinder
{
    private HelperRepository $repository;

    public function __construct(HelperRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param HelpRequest[] $helpRequests
     */
    public function matchHelpersToNeeds(array $helpRequests): array
    {
        $groceriesNeed = null;
        $babysitNeed = [];

        foreach ($helpRequests as $helpRequest) {
            if (HelpRequest::TYPE_GROCERIES === $helpRequest->helpType) {
                $groceriesNeed = $helpRequest;
            } else {
                $babysitNeed[] = $helpRequest;
            }
        }

        $localHelpers = $this->repository->findClosestHelpersTo($helpRequests[0]->zipCode);

        return [
            'groceries' => $groceriesNeed ? $this->matchGroceriesNeed($localHelpers) : null,
            'babysit' => $babysitNeed ? $this->matchBabysitNeed($babysitNeed, $localHelpers) : null,
        ];
    }

    /**
     * @param Helper[] $localHelpers
     *
     * @return Helper[]
     */
    private function matchGroceriesNeed(array $localHelpers): array
    {
        $scores = [];
        $matched = [];

        foreach ($localHelpers as $helper) {
            if (!$helper->canBuyGroceries) {
                continue;
            }

            // Prefer to match helpers only able to buy groceris to keep the other for babysit
            $score = $helper->canBabysit ? 1 : 2;

            $matched[] = ['helper' => $helper, 'score' => $score];
            $scores[] = $score;
        }

        array_multisort($scores, SORT_DESC, $matched);

        return array_values($matched);
    }

    /**
     * @param HelpRequest[] $babysitRequests
     * @param Helper[]      $localHelpers
     *
     * @return Helper[]
     */
    private function matchBabysitNeed(array $babysitRequests, array $localHelpers): array
    {
        $childrenCount = count($babysitRequests);

        $childrenAgeRange = [];
        foreach ($babysitRequests as $request) {
            $childrenAgeRange[] = $request->childAgeRange;
        }

        $scores = [];
        $matched = [];

        foreach ($localHelpers as $helper) {
            if (!$helper->canBabysit || $helper->babysitMaxChildren < $childrenCount) {
                continue;
            }

            // This helper should accept all the children to match
            foreach ($childrenAgeRange as $childAgeRange) {
                if (!in_array($childAgeRange, $helper->babysitAgeRanges, true)) {
                    continue 2;
                }
            }

            $score = 0;

            if ($helper->haveChildren) {
                $score += 2;
            }

            $matched[] = ['helper' => $helper, 'score' => $score];
            $scores[] = $score;
        }

        array_multisort($scores, SORT_DESC, $matched);

        return array_values($matched);
    }
}
