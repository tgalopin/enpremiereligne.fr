<?php

namespace App\Controller\Admin;

use App\MatchFinder\MatchFinder;
use App\MatchFinder\ZipCode;
use App\Statistics\StatisticsAggregator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/monitoring")
 */
class MonitoringController extends AbstractController
{
    /**
     * @Route("/statistics", name="admin_statistics")
     */
    public function statistics(StatisticsAggregator $aggregator): Response
    {
        return $this->render('admin/statistics.html.twig', [
            'departments' => ZipCode::DEPARTMENTS,

            'countTotalHelpersByDay' => $aggregator->countTotalHelpersByDay(),
            'countTotalOwnersByDay' => $aggregator->countTotalOwnersByDay(),

            'countTotalHelpers' => $aggregator->countTotalHelpers(),
            'countMatchedHelpers' => $aggregator->countMatchedHelpers(),
            'countTotalOwners' => $aggregator->countTotalOwners(),
            'countUnmatchedOwners' => $aggregator->countUnmatchedOwners(),

            'avgHelperAge' => $aggregator->avgHelperAge(),
            'countHelpersByDepartment' => $aggregator->countHelpersByDepartment(),

            'countGroceriesNeeds' => $aggregator->countGroceriesNeeds(),
            'countBabysitAggregatedNeeds' => $aggregator->countBabysitAggregatedNeeds(),
            'countBabysitTotalNeeds' => $aggregator->countBabysitTotalNeeds(),

            'countOwnersByJobType' => $aggregator->countOwnersByJobType(),
            'countOwnersByDepartment' => $aggregator->countOwnersByDepartment(),
        ]);
    }

    /**
     * @Route("/unmatched-list", name="admin_unmatched_list")
     */
    public function unmatchedList(MatchFinder $matchFinder): Response
    {
        return $this->render('admin/unmatched.html.twig', [
            'departments' => ZipCode::DEPARTMENTS,
            'unmatchedNeeds' => $matchFinder->findUnmatchedNeeds(),
        ]);
    }
}
