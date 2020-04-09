<?php

namespace App\Controller;

use App\Statistics\StatisticsAggregator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MapController extends AbstractController
{
    /**
     * @Route("/needs-map", name="needs_map")
     */
    public function needsMap(StatisticsAggregator $statsAgg, Request $request)
    {
        $ownerByDepartment = $statsAgg->countOwnersByDepartment();

        $dynamicStyle = '';
        foreach ($ownerByDepartment as $department) {
            $dynamicStyle .= '.departement'.$department['department'].' { fill: #a6dedd; }';
        }

        return $this->render('map/france-departments.svg.twig', [
            'dynamicStyle' => $dynamicStyle,
        ]);
    }
}
