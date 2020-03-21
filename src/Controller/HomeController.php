<?php

namespace App\Controller;

use App\Statistics\StatisticsAggregator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("", name="homepage")
     */
    public function index(StatisticsAggregator $aggregator)
    {
        $response = $this->render('home/index.html.twig', [
            'countTotalHelpers' => $aggregator->countTotalHelpers(),
            'countTotalOwners' => $aggregator->countTotalOwners(),
            'countUnmatchedOwners' => $aggregator->countUnmatchedOwners(),
        ]);

        $response->setCache([
            'public' => true,
            'max_age' => 10800, // 3h
            's_maxage' => 10800, // 3h
        ]);

        return $response;
    }
}
