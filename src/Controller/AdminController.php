<?php

namespace App\Controller;

use App\Entity\Helper;
use App\MatchFinder\MatchFinder;
use App\Model\MatchedNeeds;
use App\Repository\HelpRequestRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/matches", name="admin_matches")
     */
    public function matches(MatchFinder $matchFinder): Response
    {
        return $this->render('admin/matches.html.twig', [
            'matches' => $matchFinder->findMatchedNeeds(),
        ]);
    }

    /**
     * @Route("/match/{ownerUuid}", name="admin_match")
     */
    public function match(MatchFinder $matchFinder, string $ownerUuid): Response
    {
        return $this->render('admin/match.html.twig', [
            'match' => $matchFinder->matchOwnerNeeds($ownerUuid),
        ]);
    }

    /**
     * @Route("/match/close/{type}/{ownerUuid}/{id}", defaults={"id"=null}, name="admin_match_close")
     */
    public function close(MailerInterface $mailer, HelpRequestRepository $repository, string $type, string $ownerUuid, ?Helper $helper, Request $request): Response
    {
        $requests = $repository->findBy(['ownerUuid' => $ownerUuid, 'finished' => false]);
        if (!$requests) {
            throw $this->createNotFoundException();
        }

        if ($request->query->has('token')) {
            if (!$this->isCsrfTokenValid('admin_confirm', $request->query->get('token'))) {
                throw $this->createNotFoundException();
            }

            $repository->closeRequestsOf($ownerUuid, $helper, $type);

            $email = (new TemplatedEmail())
                ->from('team@enpremiereligne.fr')
                ->to($requests[0]->email, $helper->email)
                ->subject('[En Première Ligne] Bonne nouvelle !')
                ->htmlTemplate('emails/match_'.$type.'.html.twig')
                ->context([
                    'requester' => $requests[0],
                    'needs' => new MatchedNeeds($requests),
                    'helper' => $helper,
                ])
            ;

            $mailer->send($email);

            return $this->redirectToRoute('admin_matches');
        }

        return $this->render('admin/confirm.html.twig', [
            'type' => $type,
            'requests' => $requests,
            'ownerUuid' => $ownerUuid,
            'helper' => $helper,
        ]);
    }
}
