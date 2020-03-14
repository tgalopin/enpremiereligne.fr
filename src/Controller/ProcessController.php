<?php

namespace App\Controller;

use App\Entity\Helper;
use App\Form\CompositeHelpRequestType;
use App\Form\HelperType;
use App\Model\CompositeHelpRequest;
use App\Repository\HelpRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/process")
 */
class ProcessController extends AbstractController
{
    /**
     * @Route("/je-peux-aider", name="process_helper")
     */
    public function helper(EntityManagerInterface $manager, Request $request)
    {
        $helper = new Helper();

        $form = $this->createForm(HelperType::class, $helper);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($helper);
            $manager->flush();

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
    public function request(EntityManagerInterface $manager, HelpRequestRepository $repository, Request $request)
    {
        $helpRequest = new CompositeHelpRequest();

        $form = $this->createForm(CompositeHelpRequestType::class, $helpRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Try to associate this request to previous requests, to ease requests management afterwards
            $ownerId = $repository->findOwnerUuid($helpRequest->email);

            foreach ($helpRequest->createStandaloneRequests($ownerId) as $standaloneRequest) {
                $manager->persist($standaloneRequest);
            }

            $manager->flush();

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
    public function requesterView(Request $request, string $ownerUuid)
    {
        return new Response('TODO');
    }
}
