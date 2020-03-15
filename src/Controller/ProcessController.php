<?php

namespace App\Controller;

use App\Entity\Helper;
use App\Form\CompositeHelpRequestType;
use App\Form\HelperType;
use App\Model\CompositeHelpRequest;
use App\Repository\HelperRepository;
use App\Repository\HelpRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/process")
 */
class ProcessController extends AbstractController
{
    /**
     * @Route("/je-peux-aider", name="process_helper")
     */
    public function helper(MailerInterface $mailer, EntityManagerInterface $manager, HelperRepository $repository, Request $request)
    {
        $helper = new Helper();

        $form = $this->createForm(HelperType::class, $helper);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $helper->email = strtolower($helper->email);
            $repository->clearOldProposal($helper->email);

            $manager->persist($helper);
            $manager->flush();

            $email = (new TemplatedEmail())
                ->from('team@enpremiereligne.fr')
                ->to($helper->email)
                ->subject('Merci de vous être porté(e) volontaire sur En Première Ligne !')
                ->htmlTemplate('emails/helper.html.twig')
                ->context(['helper' => $helper])
            ;

            //$mailer->send($email);

            return $this->redirectToRoute('process_helper_view', [
                'uuid' => $helper->getUuid()->toString(),
                'success' => '1',
            ]);
        }

        return $this->render('process/helper.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/je-peux-aider/{uuid}", name="process_helper_view")
     */
    public function helperView(Helper $helper, Request $request)
    {
        return $this->render('process/helper_view.html.twig', [
            'helper' => $helper,
            'success' => $request->query->getBoolean('success'),
        ]);
    }

    /**
     * @Route("/je-peux-aider/{uuid}/supprimer", name="process_helper_delete_confirm")
     */
    public function helperDeleteConfirm(Helper $helper)
    {
        return $this->render('process/helper_delete_confirm.html.twig', ['helper' => $helper]);
    }

    /**
     * @Route("/je-peux-aider/{uuid}/supprimer/do", name="process_helper_delete_do")
     */
    public function helperDeleteDo(EntityManagerInterface $manager, Helper $helper, Request $request)
    {
        if (!$this->isCsrfTokenValid('helper_delete', $request->query->get('token'))) {
            throw $this->createNotFoundException();
        }

        $manager->remove($helper);
        $manager->flush();

        return $this->redirectToRoute('process_helper_delete_done');
    }

    /**
     * @Route("/je-peux-aider/supprimer/effectue", name="process_helper_delete_done")
     */
    public function helperDeleted()
    {
        return $this->render('process/helper_delete_done.html.twig');
    }

    /**
     * @Route("/j-ai-besoin-d-aide", name="process_request")
     */
    public function request(MailerInterface $mailer, EntityManagerInterface $manager, HelpRequestRepository $repository, Request $request)
    {
        $helpRequest = new CompositeHelpRequest();

        $form = $this->createForm(CompositeHelpRequestType::class, $helpRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->clearOldOwnerRequests($helpRequest->email);

            $ownerId = Uuid::uuid4();
            foreach ($helpRequest->createStandaloneRequests($ownerId) as $standaloneRequest) {
                $manager->persist($standaloneRequest);
            }

            $manager->flush();

            $email = (new TemplatedEmail())
                ->from('team@enpremiereligne.fr')
                ->to($helpRequest->email)
                ->subject('Nous avons bien reçu votre demande sur En Première Ligne')
                ->htmlTemplate('emails/request.html.twig')
                ->context(['request' => $helpRequest, 'ownerUuid' => $ownerId])
            ;

            $mailer->send($email);

            return $this->redirectToRoute('process_requester_view', [
                'ownerUuid' => $ownerId->toString(),
                'success' => '1',
            ]);
        }

        return $this->render('process/request.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/j-ai-besoin-d-aide/{ownerUuid}", name="process_requester_view")
     */
    public function requesterView(HelpRequestRepository $repository, Request $request, string $ownerUuid)
    {
        $needs = $repository->findBy(['ownerUuid' => $ownerUuid], ['createdAt' => 'DESC']);
        if (!$needs) {
            throw $this->createNotFoundException();
        }

        return $this->render('process/request_owner_view.html.twig', [
            'needs' => $repository->findBy(['ownerUuid' => $ownerUuid], ['createdAt' => 'DESC']),
            'success' => $request->query->getBoolean('success'),
        ]);
    }

    /**
     * @Route("/j-ai-besoin-d-aide/{ownerUuid}/supprimer", name="process_requester_delete_confirm")
     */
    public function requestDeleteConfirm(string $ownerUuid)
    {
        return $this->render('process/request_owner_delete_confirm.html.twig', ['ownerUuid' => $ownerUuid]);
    }

    /**
     * @Route("/j-ai-besoin-d-aide/{ownerUuid}/supprimer/do", name="process_requester_delete_do")
     */
    public function requestDeleteDo(HelpRequestRepository $repository, Request $request, string $ownerUuid)
    {
        if (!$this->isCsrfTokenValid('requester_delete', $request->query->get('token'))) {
            throw $this->createNotFoundException();
        }

        $repository->clearOwnerRequestsByUuid($ownerUuid);

        return $this->redirectToRoute('process_requester_delete_done');
    }

    /**
     * @Route("/j-ai-besoin-d-aide/supprimer/effectue", name="process_requester_delete_done")
     */
    public function requestDeleted()
    {
        return $this->render('process/request_owner_delete_done.html.twig');
    }
}
