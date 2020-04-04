<?php

namespace App\Controller;

use App\Statistics\StatisticsAggregator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/map")
 */
class MapController extends AbstractController
{
    const DEPARTMENT_FILL_COLOR = '#a6dedd';
    const DEPARTMENT_COLORING_THRESHOLD = 2; // Default min number of request owners in department to color it

    /**
     * @Route("/json")
     */
    public function jsonStats(StatisticsAggregator $statsAgg)
    {
        $ownerByDepartment = $statsAgg->countOwnersByDepartment();

        return new JsonResponse($ownerByDepartment);
    }

    /**
     * @Route("/svg")
     */
    public function svg(StatisticsAggregator $statsAgg, Request $request)
    {
        $threshold = $request->query->getInt('threshold', self::DEPARTMENT_COLORING_THRESHOLD);

        $dynamicStyle = '';
        $fillColor = self::DEPARTMENT_FILL_COLOR;
        $ownerByDepartment = $statsAgg->countOwnersByDepartment();
        foreach ($ownerByDepartment as $department) {
            if ($department['nb'] >= $threshold) {
                $dynamicStyle .= ".departement${department['department']} { fill: ${fillColor}; }";
            }
        }

        $response = $this->render('map/france-departments.svg.twig', [
            'dynamicStyle' => $dynamicStyle,
        ]);

        $response->setCache([
            'public' => true,
            'max_age' => 900, // 15min
            's_maxage' => 900, // 15min
        ]);

        return $response;
    }
}
