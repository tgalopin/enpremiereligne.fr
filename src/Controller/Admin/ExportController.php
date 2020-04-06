<?php

namespace App\Controller\Admin;

use App\MatchFinder\MatchFinder;
use App\MatchFinder\ZipCode;
use App\Repository\HelperRepository;
use League\Csv\Writer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/admin/export")
 */
class ExportController extends AbstractController
{
    /**
     * @Route("/helpers", name="admin_export_helpers")
     */
    public function helpers(HelperRepository $repository, TranslatorInterface $translator): Response
    {
        $csv = $this->createCsvWriter();
        $csv->insertOne([
            'E-mail',
            $translator->trans('label.name-first'),
            $translator->trans('label.name-last'),
        ]);

        $helpers = $repository->exportAll();
        foreach ($helpers as $helper) {
            $csv->insertOne([$helper['email'], $helper['firstName'], $helper['lastName']]);
        }

        return $this->createCsvResponse('helpers-all-'.date('Y-m-d-H-i').'.csv', $csv->getContent());
    }

    /**
     * @Route("/helpers/by-zip-code", name="admin_export_helpers_by_zip_code")
     */
    public function helpersByZipCode(HelperRepository $repository, TranslatorInterface $translator): Response
    {
        $csv = $this->createCsvWriter();
        $csv->insertOne([
            $translator->trans('label.postcode'),
            $translator->trans('admin.number'),
        ]);

        $zipCodes = $repository->exportByZipCode();
        foreach ($zipCodes as $zipCode) {
            $csv->insertOne([$zipCode['zipCode'], $zipCode['nb']]);
        }

        return $this->createCsvResponse('helpers-zip-codes-'.date('Y-m-d-H-i').'.csv', $csv->getContent());
    }

    /**
     * @Route("/unmatched", name="admin_export_unmatched")
     */
    public function unmatched(MatchFinder $matchFinder, TranslatorInterface $translator, string $locale): Response
    {
        $csv = $this->createCsvWriter();
        $csv->insertOne([
            'ID',
            $translator->trans('admin.department'),
            $translator->trans('label.name-first'),
            $translator->trans('label.postcode'),
            $translator->trans('label.need'),
        ]);

        foreach ($matchFinder->findUnmatchedNeeds() as $matches) {
            foreach ($matches as $match) {
                if ($need = $match->getGroceriesNeed()) {
                    $csv->insertOne([
                        $need->getId(),
                        ZipCode::DEPARTMENTS[$locale][substr($need->zipCode, 0, 2)] ?? '',
                        $need->firstName,
                        str_pad($need->zipCode, 5, ' ', STR_PAD_LEFT),
                        ucfirst($translator->trans('label.groceries')),
                    ]);
                }

                foreach ($match->getBabysitNeeds() as $need) {
                    $csv->insertOne([
                        $need->getId(),
                        ZipCode::DEPARTMENTS[$locale][substr($need->zipCode, 0, 2)] ?? '',
                        $need->firstName,
                        str_pad($need->zipCode, 5, ' ', STR_PAD_LEFT),
                        $translator->trans('admin.export-babysit', ['ages' => $need->childAgeRange]),
                    ]);
                }
            }
        }

        return $this->createCsvResponse('besoins-'.date('Y-m-d-H-i').'.csv', $csv->getContent());
    }

    private function createCsvWriter(): Writer
    {
        $csv = Writer::createFromString();
        $csv->setDelimiter(',');
        $csv->setOutputBOM(Writer::BOM_UTF8);

        return $csv;
    }

    private function createCsvResponse(string $name, string $content): Response
    {
        $response = new Response($content);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $name
        ));

        return $response;
    }
}
