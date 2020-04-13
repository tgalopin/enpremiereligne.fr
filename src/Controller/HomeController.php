<?php

namespace App\Controller;

use App\Entity\Invitation;
use App\Form\HomepageInvitationType;
use App\Model\HomepageInvitation;
use App\Repository\InvitationRepository;
use App\Statistics\StatisticsAggregator;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("", name="homepage")
     */
    public function index(StatisticsAggregator $aggregator)
    {
        $response = $this->render('home/index.html.twig', [
            'inviteForm' => $this->createForm(HomepageInvitationType::class)->createView(),
            'countTotalHelpers' => $aggregator->countTotalHelpers(),
            'countTotalOwners' => $aggregator->countTotalOwners(),
            'countUnmatchedOwners' => $aggregator->countUnmatchedOwners(),
        ]);

        $response->setCache([
            'public' => true,
            'max_age' => 900, // 15min
            's_maxage' => 900, // 15min
        ]);

        return $response;
    }

    /**
     * @Route("/invite", name="invite")
     */
    public function invite(StatisticsAggregator $aggregator, InvitationRepository $repository, MailerInterface $mailer, Request $request)
    {
        $invite = new HomepageInvitation();
        $invite->firstName = $request->query->get('f');

        $form = $this->createForm(HomepageInvitationType::class, $invite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = Invitation::hashEmail($invite->email);

            if (!$repository->isHashAlreadyInvited($hash)) {
                $email = (new TemplatedEmail())
                    ->from('team@enpremiereligne.fr')
                    ->to($invite->email)
                    ->subject($invite->firstName.' pense qu\'En Première Ligne peut vous aider !')
                    ->htmlTemplate('emails/fr_FR/invite.html.twig')
                    ->context([
                        'invite' => $invite,
                        'countTotalHelpers' => ceil($aggregator->countTotalHelpers() / 100) * 100,
                        'countZipCodeHelpers' => $aggregator->countZipCodeHelpers($request->getLocale(), $invite->zipCode),
                    ])
                ;

                $mailer->send($email);

                $repository->persistInvitationHash($hash);
            }

            return $this->redirectToRoute('invite', ['s' => 1]);
        }

        return $this->render('home/invite.html.twig', [
            'form' => $form->createView(),
            'success' => $request->query->getBoolean('s'),
        ]);
    }
}
