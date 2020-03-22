<?php

namespace App\MatchFinder;

use App\Entity\Helper;
use App\Entity\HelpRequest;
use App\Model\Match;
use App\Model\MatchedNeed;
use App\Model\MatchedNeeds;
use App\Repository\BlockedMatchRepository;
use App\Repository\HelperRepository;
use App\Repository\HelpRequestRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MatchFinder
{
    private HelpRequestRepository $helpRequestRepo;
    private HelperRepository $helperRepo;
    private BlockedMatchRepository $blockedMatchRepo;

    private array $zipCodeCache;

    public function __construct(HelpRequestRepository $helpRequestRepo, HelperRepository $helperRepo, BlockedMatchRepository $blockedMatchRepo)
    {
        $this->helpRequestRepo = $helpRequestRepo;
        $this->helperRepo = $helperRepo;
        $this->blockedMatchRepo = $blockedMatchRepo;
    }

    /**
     * @return MatchedNeeds[]
     */
    public function findMatchedNeeds(): array
    {
        $owners = $this->helpRequestRepo->findNeedsByOwner(['finished' => false], ['createdAt' => 'DESC']);

        $matchedNeeds = [];
        $scores = [];
        $dates = [];

        foreach ($owners as $ownerNeeds) {
            $groceriesNeed = null;
            $babysitNeeds = [];

            foreach ($ownerNeeds as $need) {
                if (HelpRequest::TYPE_GROCERIES === $need->helpType) {
                    $groceriesNeed = $need;
                } else {
                    $babysitNeeds[] = $need;
                }
            }

            $blockedHelpers = $this->blockedMatchRepo->findBlockedHelpersIdsFor($ownerNeeds[0]->ownerUuid->toString());

            $matchedGroceriesNeed = null;
            $matchedBabysitNeed = null;
            $score = 0;

            if ($groceriesNeed) {
                $matchedGroceriesNeed = $this->matchGroceriesNeed($groceriesNeed, $blockedHelpers);

                if ($matchedGroceriesNeed->getMatchedHelpers()) {
                    $score = 1;
                }
            }

            if ($babysitNeeds) {
                $matchedBabysitNeed = $this->matchBabysitNeed($babysitNeeds, $blockedHelpers);

                if ($matchedBabysitNeed->getMatchedHelpers()) {
                    $score = 1;
                }
            }

            $matchedNeeds[] = new MatchedNeeds($ownerNeeds, $matchedGroceriesNeed, $matchedBabysitNeed, $score);
            $scores[] = $score;
            $dates[] = (float) $ownerNeeds[0]->getCreatedAt()->format('U');
        }

        array_multisort($scores, SORT_DESC, $dates, SORT_ASC, $matchedNeeds);

        return array_values($matchedNeeds);
    }

    public function matchOwnerNeeds(string $ownerUuid): MatchedNeeds
    {
        if (!$needs = $this->helpRequestRepo->findBy(['ownerUuid' => $ownerUuid, 'finished' => false])) {
            throw new NotFoundHttpException();
        }

        $groceriesNeed = null;
        $babysitNeeds = [];

        foreach ($needs as $need) {
            if (HelpRequest::TYPE_GROCERIES === $need->helpType) {
                $groceriesNeed = $need;
            } else {
                $babysitNeeds[] = $need;
            }
        }

        $blockedHelpers = $this->blockedMatchRepo->findBlockedHelpersIdsFor($needs[0]->ownerUuid->toString());

        return new MatchedNeeds(
            $needs,
            $groceriesNeed ? $this->matchGroceriesNeed($groceriesNeed, $blockedHelpers) : null,
            $babysitNeeds ? $this->matchBabysitNeed($babysitNeeds, $blockedHelpers) : null
        );
    }

    private function matchGroceriesNeed(HelpRequest $need, array $blockedHelpersIds): ?MatchedNeed
    {
        $localHelpers = $this->findLocalHelpers($need->zipCode);

        $scores = [];
        $matched = [];

        foreach ($localHelpers as $helper) {
            if (!$helper->canBuyGroceries && !$helper->acceptVulnerable) {
                continue;
            }

            if (in_array($helper->getId(), $blockedHelpersIds)) {
                continue;
            }

            if ($need->jobType === 'vulnerable' && !$helper->acceptVulnerable) {
                continue;
            }

            $score = 0;

            // Prefer to match helpers in the same zip code vs the closest one
            if ($helper->zipCode === $need->zipCode) {
                ++$score;
            }

            // Prefer to match helpers only able to buy groceris to keep the other for babysit
            if (!$helper->canBabysit) {
                ++$score;
            }

            $matched[] = new Match($need, $helper, $score);
            $scores[] = $score;
        }

        array_multisort($scores, SORT_DESC, $matched);

        return new MatchedNeed($need, array_values($matched));
    }

    /**
     * @param HelpRequest[] $needs
     */
    private function matchBabysitNeed(array $needs, array $blockedHelpersIds): ?MatchedNeed
    {
        $localHelpers = $this->findLocalHelpers($needs[0]->zipCode);
        $childrenCount = count($needs);

        $childrenAgeRange = [];
        foreach ($needs as $need) {
            $childrenAgeRange[] = $need->childAgeRange;
        }

        $scores = [];
        $matched = [];

        foreach ($localHelpers as $helper) {
            if (!$helper->canBabysit
                || $helper->babysitMaxChildren < $childrenCount
                || in_array($helper->getId(), $blockedHelpersIds)) {
                continue;
            }

            // This helper should accept all the children to match
            foreach ($childrenAgeRange as $childAgeRange) {
                if (!in_array($childAgeRange, $helper->babysitAgeRanges, true)) {
                    continue 2;
                }
            }

            $score = 0;

            // Prefer to match helpers in the same zip code vs the closest one
            if ($helper->zipCode === $needs[0]->zipCode) {
                ++$score;
            }

            // Prefer to match helpers who are parents
            if ($helper->haveChildren) {
                ++$score;
            }

            $matched[] = new Match($needs[0], $helper, $score);
            $scores[] = $score;
        }

        array_multisort($scores, SORT_DESC, $matched);

        return new MatchedNeed($needs[0], array_values($matched));
    }

    /**
     * @return Helper[]
     */
    private function findLocalHelpers(string $zipCode): iterable
    {
        if (isset($this->zipCodeCache[$zipCode])) {
            return $this->zipCodeCache[$zipCode];
        }

        return $this->zipCodeCache[$zipCode] = $this->helperRepo->findClosestHelpersTo($zipCode);
    }
}
