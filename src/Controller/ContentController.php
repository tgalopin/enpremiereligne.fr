<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/page")
 */
class ContentController extends AbstractController
{
    private string $folder;

    public function __construct(string $locale)
    {
        $this->folder = $locale.'/';
    }

    /**
     * @Route({
     *     "fr_FR": "/comment-ca-marche",
     *     "en_NZ": "/how-this-works"
     * }, name="content_how")
     */
    public function how()
    {
        return $this->render('content/'.$this->folder.'how.html.twig');
    }

    /**
     * @Route("/international", name="content_international")
     */
    public function international()
    {
        return $this->render('content/'.$this->folder.'international.html.twig');
    }

    /**
     * @Route({
     *     "fr_FR": "/pourquoi",
     *     "en_NZ": "/why"
     * }, name="content_why")
     */
    public function why()
    {
        return $this->render('content/'.$this->folder.'why.html.twig');
    }

    /**
     * @Route({
     *     "fr_FR": "/qui-sommes-nous",
     *     "en_NZ": "/who-are-we"
     * }, name="content_who")
     */
    public function who()
    {
        return $this->render('content/'.$this->folder.'who.html.twig');
    }

    /**
     * @Route({
     *     "fr_FR": "/politique-de-confidentialite",
     *     "en_NZ": "/privacy-policy"
     * }, name="content_privacy")
     */
    public function privacy()
    {
        return $this->render('content/'.$this->folder.'privacy.html.twig');
    }

    /**
     * @Route({
     *     "fr_FR": "/conditions-d-utilisation",
     *     "en_NZ": "/terms-of-use"
     * }, name="content_conditions")
     */
    public function conditions()
    {
        return $this->render('content/'.$this->folder.'conditions.html.twig');
    }

    /**
     * @Route({
     *     "fr_FR": "/mentions-legales",
     *     "en_NZ": "/legal-statement"
     * }, name="content_legalities")
     */
    public function legalities()
    {
        return $this->render('content/'.$this->folder.'legalities.html.twig');
    }

    /**
     * @Route({
     *     "fr_FR": "/informations-officielles",
     *     "en_NZ": "/official-information"
     * }, name="content_links")
     */
    public function links()
    {
        return $this->render('content/'.$this->folder.'links.html.twig');
    }

    /**
     * @Route({
     *     "fr_FR": "/presse"
     * }, name="content_press")
     */
    public function press()
    {
        return $this->render('content/'.$this->folder.'press.html.twig');
    }
}
