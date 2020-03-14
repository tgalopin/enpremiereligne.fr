<?php

namespace App\Controller;

use App\Entity\Helper;
use App\Form\HelperFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

        $form = $this->createForm(HelperFormType::class, $helper);
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
     * @Route("/j-ai-besoin-d-aide", name="process_request")
     */
    public function request()
    {
        return $this->render('home/index.html.twig');
    }

    /**
     * @Route("/je-peux-aider/{uuid}", name="process_helper_view")
     */
    public function helperView(Helper $helper, Request $request)
    {
        dump($helper);
        exit;
    }
}
