<?php

namespace App\Controller;

use App\Entity\Helper;
use App\Form\CompositeHelpRequestType;
use App\Form\HelperType;
use App\Form\VulnerableHelpRequestType;
use App\Model\CompositeHelpRequest;
use App\Model\VulnerableHelpRequest;
use App\Repository\HelperRepository;
use App\Repository\HelpRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/process")
 */
class ProcessController extends AbstractController
{
    /**
     * @Route({
     *     "fr_FR": "/je-peux-aider",
     *     "en_NZ": "/i-can-help"
     * }, name="process_helper")
     */
    public function helper(MailerInterface $mailer, EntityManagerInterface $manager, HelperRepository $repository, Request $request, TranslatorInterface $translator, string $sender)
    {
        $helper = new Helper();

        $form = $this->createForm(HelperType::class, $helper);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $helper->email = strtolower($helper->email);
            $repository->removeHelpProposal($helper->email);

            $manager->persist($helper);
            $manager->flush();

            $email = (new TemplatedEmail())
                ->from($sender)
                ->to($helper->email)
                ->subject($translator->trans('email.offer-thanks-subject'))
                ->htmlTemplate('emails/helper.html.twig')
                ->context(['helper' => $helper])
            ;

            $mailer->send($email);

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
     * @Route({
     *     "fr_FR": "/je-peux-aider/{uuid}",
     *     "en_NZ": "/i-can-help/{uuid}"
     * }, name="process_helper_view")
     */
    public function helperView(Helper $helper, Request $request)
    {
        return $this->render('process/helper_view.html.twig', [
            'helper' => $helper,
            'success' => $request->query->getBoolean('success'),
        ]);
    }

    /**
     * @Route({
     *     "fr_FR": "/je-peux-aider/{uuid}/supprimer",
     *     "en_NZ": "/i-can-help/{uuid}/remove"
     * }, name="process_helper_delete_confirm")
     */
    public function helperDeleteConfirm(Helper $helper)
    {
        return $this->render('process/helper_delete_confirm.html.twig', ['helper' => $helper]);
    }

    /**
     * @Route({
     *     "fr_FR": "/je-peux-aider/{uuid}/supprimer/do",
     *     "en_NZ": "/i-can-help/{uuid}/remove/process"
     * }, name="process_helper_delete_do")
     */
    public function helperDeleteDo(HelperRepository $repository, Helper $helper, Request $request)
    {
        if (!$this->isCsrfTokenValid('helper_delete', $request->query->get('token'))) {
            throw $this->createNotFoundException();
        }

        $repository->removeHelpProposal($helper->email);

        return $this->redirectToRoute('process_helper_delete_done');
    }

    /**
     * @Route({
     *     "fr_FR": "/je-peux-aider/supprimer/effectue",
     *     "en_NZ": "/i-can-help/remove/done"
     * }, name="process_helper_delete_done")
     */
    public function helperDeleted()
    {
        return $this->render('process/helper_delete_done.html.twig');
    }

    /**
     * @Route({
     *     "fr_FR": "/j-ai-besoin-d-aide",
     *     "en_NZ": "/i-need-help"
     * }, name="process_request")
     */
    public function request(MailerInterface $mailer, EntityManagerInterface $manager, HelpRequestRepository $repository, Request $request, TranslatorInterface $translator, string $sender)
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
                ->from($sender)
                ->to($helpRequest->email)
                ->subject($translator->trans('email.request-subject'))
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
     * @Route({
     *     "fr_FR": "/j-ai-besoin-d-aide-risque",
     *     "en_NZ": "/at-risk-need-help"
     * }, name="process_request_vulnerable")
     */
    public function requestVulnerable(MailerInterface $mailer, EntityManagerInterface $manager, HelpRequestRepository $repository, Request $request, TranslatorInterface $translator, string $sender)
    {
        $helpRequest = new VulnerableHelpRequest();

        $form = $this->createForm(VulnerableHelpRequestType::class, $helpRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->clearOldOwnerRequests($helpRequest->email);

            $ownerId = Uuid::uuid4();

            $manager->persist($helpRequest->createStandaloneRequest($ownerId));
            $manager->flush();

            $to = [$helpRequest->email];
            if ($helpRequest->ccEmail) {
                $to[] = $helpRequest->ccEmail;
            }

            $email = (new TemplatedEmail())
                ->from($sender)
                ->to(...$to)
                ->subject($translator->trans('email.request-subject'))
                ->htmlTemplate('emails/request_vulnerable.html.twig')
                ->context(['request' => $helpRequest, 'ownerUuid' => $ownerId])
            ;

            $mailer->send($email);

            return $this->redirectToRoute('process_requester_view', [
                'ownerUuid' => $ownerId->toString(),
                'success' => '1',
            ]);
        }

        return $this->render('process/request_vulnerable.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route({
     *     "fr_FR": "/j-ai-besoin-d-aide/{ownerUuid}",
     *     "en_NZ": "/i-need-help/{ownerUuid}"
     * }, name="process_requester_view")
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
     * @Route({
     *     "fr_FR": "/j-ai-besoin-d-aide/{ownerUuid}/supprimer",
     *     "en_NZ": "/i-need-help/{ownerUuid}/remove"
     * }, name="process_requester_delete_confirm")
     */
    public function requestDeleteConfirm(string $ownerUuid)
    {
        return $this->render('process/request_owner_delete_confirm.html.twig', ['ownerUuid' => $ownerUuid]);
    }

    /**
     * @Route({
     *     "fr_FR": "/j-ai-besoin-d-aide/{ownerUuid}/supprimer/do",
     *     "en_NZ": "/i-need-help/{ownerUuid}/remove/process"
     * }, name="process_requester_delete_do")
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
     * @Route({
     *     "fr_FR": "/j-ai-besoin-d-aide/supprimer/effectue",
     *     "en_NZ": "/i-need-help/{ownerUuid}/remove/done"
     * }, name="process_requester_delete_done")
     */
    public function requestDeleted()
    {
        return $this->render('process/request_owner_delete_done.html.twig');
    }
}
