<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/page")
 */
class ContentController extends AbstractController
{
    /** @var string */
    protected $folder;

    public function __construct(string $locale)
    {
        $this->folder = 'fr' === $locale ? '' : $locale .'/';
    }

    /**
     * @Route({
     *     "fr": "/comment-ca-marche",
     *     "en": "/how-this-works"
     * }, name="content_how")
     */
    public function how()
    {
        return $this->render('content/' . $this->folder . 'how.html.twig');
    }

    /**
     * @Route("/international", name="content_international")
     */
    public function international()
    {
        return $this->render('content/' . $this->folder . 'international.html.twig');
    }

    /**
     * @Route({
     *     "fr": "/pourquoi",
     *     "en": "/why"
     * }, name="content_why")
     */
    public function why()
    {
        return $this->render('content/' . $this->folder . 'why.html.twig');
    }

    /**
     * @Route({
     *     "fr": "/qui-sommes-nous",
     *     "en": "/who-are-we"
     * }, name="content_who")
     */
    public function who()
    {
        return $this->render('content/' . $this->folder . 'who.html.twig');
    }

    /**
     * @Route({
     *     "fr": "/politique-de-confidentialite",
     *     "en": "/privacy-policy"
     * }, name="content_privacy")
     */
    public function privacy()
    {
        return $this->render('content/' . $this->folder . 'privacy.html.twig');
    }

    /**
     * @Route({
     *     "fr": "/conditions-d-utilisation",
     *     "en": "/terms-of-use"
     * }, name="content_conditions")
     */
    public function conditions()
    {
        return $this->render('content/' . $this->folder . 'conditions.html.twig');
    }

    /**
     * @Route({
     *     "fr": "/mentions-legales",
     *     "en": "/legal-statement"
     * }, name="content_legalities")
     */
    public function legalities()
    {
        return $this->render('content/' . $this->folder . 'legalities.html.twig');
    }

    /**
     * @Route({
     *     "fr": "/informations-officielles",
     *     "en": "/official-information"
     * }, name="content_links")
     */
    public function links()
    {
        return $this->render('content/' . $this->folder . 'links.html.twig');
    }
}
