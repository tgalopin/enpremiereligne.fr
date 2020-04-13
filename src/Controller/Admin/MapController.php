<?php

namespace App\Controller\Admin;

use App\Statistics\StatisticsAggregator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/map")
 */
class MapController extends AbstractController
{
    /**
     * @Route("/requests", name="admin_map_requests")
     */
    public function requests(StatisticsAggregator $statsAgg)
    {
        return $this->render('admin/map/fr_FR/requests.svg.twig', [
            'departmentCounts' => $statsAgg->countOwnersByDepartment(),
        ]);
    }

    /**
     * @Route("/helpers", name="admin_map_helpers")
     */
    public function helpers(StatisticsAggregator $statsAgg)
    {
        $departments = $statsAgg->countHelpersByDepartment();
        array_multisort(array_column($departments, 'department'), SORT_ASC, SORT_NATURAL, $departments);

        return $this->render('admin/map/helpers.html.twig', [
            'departments' => $departments,
        ]);
    }

    /**
     * @Route("/helpers/department/{number}.svg", name="admin_map_helpers_department")
     */
    public function helpersDepartment(StatisticsAggregator $statsAgg, string $number)
    {
        $response = $this->render('admin/map/fr_FR/helpers_department.svg.twig', [
            'department' => $number,
            'helpersCount' => $statsAgg->countDepartmentHelpers($number),
        ]);

        $response->headers->set('Content-Type', 'image/svg+xml');

        return $response;
    }
}
