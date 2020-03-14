<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/page")
 */
class ContentController extends AbstractController
{
    /**
     * @Route("/comment-ca-marche", name="content_how")
     */
    public function how()
    {
        return new Response('TODO');
    }

    /**
     * @Route("/pourquoi", name="content_why")
     */
    public function why()
    {
        return new Response('TODO');
    }

    /**
     * @Route("/politique-de-confidentialite", name="content_privacy")
     */
    public function privacy()
    {
        return new Response('TODO');
    }

    /**
     * @Route("/conditions-d-utilisation", name="content_conditions")
     */
    public function conditions()
    {
        return new Response('TODO');
    }

    /**
     * @Route("/mentions-legales", name="content_legalities")
     */
    public function legalities()
    {
        return new Response('TODO');
    }
}
