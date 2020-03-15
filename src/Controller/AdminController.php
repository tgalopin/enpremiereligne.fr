<?php

namespace App\Controller;

use App\Entity\Helper;
use App\MatchFinder\MatchFinder;
use App\Repository\HelpRequestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/matches", name="admin_matches")
     */
    public function matches(HelpRequestRepository $repository): Response
    {
        $requests = $repository->findBy(['finished' => false], ['createdAt' => 'DESC']);

        $requesters = [];
        foreach ($requests as $request) {
            if (!isset($requesters[$request->ownerUuid->toString()])) {
                $requesters[$request->ownerUuid->toString()] = [];
            }

            $requesters[$request->ownerUuid->toString()][] = $request;
        }

        return $this->render('admin/matches.html.twig', [
            'requesters' => $requesters,
        ]);
    }

    /**
     * @Route("/match/{ownerUuid}", name="admin_match")
     */
    public function match(HelpRequestRepository $repository, MatchFinder $matchFinder, string $ownerUuid): Response
    {
        $requests = $repository->findBy(['ownerUuid' => $ownerUuid, 'finished' => false]);
        if (!$requests) {
            throw $this->createNotFoundException();
        }

        return $this->render('admin/match.html.twig', [
            'requests' => $requests,
            'matched' => $matchFinder->matchHelpersToNeeds($requests),
        ]);
    }

    /**
     * @Route("/match/close/{ownerUuid}/{id}", defaults={"id"=null}, name="admin_match_close_groceries")
     */
    public function closeGroceries(HelpRequestRepository $repository, string $ownerUuid, ?Helper $helper, Request $request): Response
    {
        $requests = $repository->findBy(['ownerUuid' => $ownerUuid, 'finished' => false]);
        if (!$requests) {
            throw $this->createNotFoundException();
        }

        if ($request->query->has('token')) {
            if (!$this->isCsrfTokenValid('admin_confirm', $request->query->get('token'))) {
                throw $this->createNotFoundException();
            }

            $repository->closeGroceriesFor($ownerUuid, $helper);

            return $this->redirectToRoute('admin_matches');
        }

        return $this->render('admin/confirm.html.twig', [
            'type' => 'groceries',
            'requests' => $requests,
            'ownerUuid' => $ownerUuid,
            'helper' => $helper,
        ]);
    }
}
