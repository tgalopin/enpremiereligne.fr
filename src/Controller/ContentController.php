<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        return $this->render('content/how.html.twig');
    }

    /**
     * @Route("/pourquoi", name="content_why")
     */
    public function why()
    {
        return $this->render('content/why.html.twig');
    }

    /**
     * @Route("/presse", name="content_press")
     */
    public function press()
    {
        return $this->render('content/press.html.twig');
    }

    /**
     * @Route("/politique-de-confidentialite", name="content_privacy")
     */
    public function privacy()
    {
        return $this->render('content/privacy.html.twig');
    }

    /**
     * @Route("/conditions-d-utilisation", name="content_conditions")
     */
    public function conditions()
    {
        return $this->render('content/conditions.html.twig');
    }

    /**
     * @Route("/mentions-legales", name="content_legalities")
     */
    public function legalities()
    {
        return $this->render('content/legalities.html.twig');
    }
}
