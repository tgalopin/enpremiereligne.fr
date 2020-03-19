<?php

namespace App\Tests\Controller;

use App\Entity\Helper;
use App\Entity\HelpRequest;
use App\Repository\HelperRepository;
use App\Repository\HelpRequestRepository;
use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProcessControllerTest extends WebTestCase
{
    public function testHelperCreate()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/process/je-peux-aider');
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Envoyer ma proposition');
        $this->assertCount(1, $button);

        $client->submit($button->form(), [
            'helper[firstName]' => 'Titouan',
            'helper[lastName]' => 'Galopin',
            'helper[age]' => '25',
            'helper[zipCode]' => '75008',
            'helper[email]' => 'titouan.galopin@example.com',
            'helper[canBuyGroceries]' => true,
            'helper[canBabysit]' => true,
            'helper[haveChildren]' => true,
            'helper[babysitMaxChildren]' => 3,
            'helper[babysitAgeRanges]' => ['0-1'],
            'helper[confirm]' => true,
            'helper[c]' => '',
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $helper = self::$container->get(HelperRepository::class)->findOneBy(['email' => 'titouan.galopin@example.com']);
        $this->assertInstanceOf(Helper::class, $helper);
        $this->assertSame('Titouan', $helper->firstName);
        $this->assertSame('Galopin', $helper->lastName);
        $this->assertSame(25, $helper->age);
        $this->assertSame('75008', $helper->zipCode);
        $this->assertSame('titouan.galopin@example.com', $helper->email);
        $this->assertSame(true, $helper->canBuyGroceries);
        $this->assertSame(true, $helper->canBabysit);
        $this->assertSame(true, $helper->haveChildren);
        $this->assertSame(3, $helper->babysitMaxChildren);
        $this->assertSame(['0-1'], $helper->babysitAgeRanges);
    }

    public function testHelperRequiresConfirm()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/process/je-peux-aider');
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $button = $crawler->selectButton('Envoyer ma proposition');
        $this->assertCount(1, $button);

        $client->submit($button->form(), [
            'helper[firstName]' => 'Titouan',
            'helper[lastName]' => 'Galopin',
            'helper[age]' => '25',
            'helper[zipCode]' => '75008',
            'helper[email]' => 'titouan.galopin@example.com',
            'helper[canBuyGroceries]' => true,
            'helper[canBabysit]' => true,
            'helper[haveChildren]' => true,
            'helper[babysitMaxChildren]' => 3,
            'helper[babysitAgeRanges]' => ['0-1'],
        ]);
        $this->assertResponseIsSuccessful($client->getResponse()->getStatusCode());
    }

    public function testHelperRequiresc()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/process/je-peux-aider');
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Envoyer ma proposition');
        $this->assertCount(1, $button);

        $client->submit($button->form(), [
            'helper[firstName]' => 'Titouan',
            'helper[lastName]' => 'Galopin',
            'helper[age]' => '25',
            'helper[zipCode]' => '75008',
            'helper[email]' => 'titouan.galopin@example.com',
            'helper[canBuyGroceries]' => true,
            'helper[canBabysit]' => true,
            'helper[haveChildren]' => true,
            'helper[babysitMaxChildren]' => 3,
            'helper[babysitAgeRanges]' => ['0-1'],
            'helper[confirm]' => true,
            'helper[c]' => 'blocking bot',
        ]);
        $this->assertResponseIsSuccessful($client->getResponse()->getStatusCode());
    }

    public function testHelperViewDelete()
    {
        $client = static::createClient();

        $helper = self::$container->get(HelperRepository::class)->findOneBy(['email' => 'elizabeth.gregory@example.com']);
        $this->assertInstanceOf(Helper::class, $helper);

        $crawler = $client->request('GET', '/process/je-peux-aider/'.$helper->getUuid()->toString());
        $this->assertResponseIsSuccessful();
        $link = $crawler->filter('a:contains(\'Supprimer ma proposition\')');
        $this->assertCount(1, $link);

        $crawler = $client->click($link->link());
        $link = $crawler->filter('a:contains(\'Oui, supprimer\')');
        $this->assertCount(1, $link);

        $client->click($link->link());
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $helper = self::$container->get(HelperRepository::class)->findOneBy(['email' => 'elizabeth.gregory@example.com']);
        $this->assertNull($helper);
    }

    public function testRequesterViewDelete()
    {
        $client = static::createClient();

        $request = self::$container->get(HelpRequestRepository::class)->findOneBy(['email' => 'jeanne.martin@example.com']);
        $this->assertInstanceOf(HelpRequest::class, $request);

        $crawler = $client->request('GET', '/process/j-ai-besoin-d-aide/'.$request->ownerUuid->toString());
        $this->assertResponseIsSuccessful();

        $link = $crawler->filter('a:contains(\'Supprimer ma demande\')');
        $this->assertCount(1, $link);

        $crawler = $client->click($link->link());
        $link = $crawler->filter('a:contains(\'Oui, supprimer\')');
        $this->assertCount(1, $link);

        $client->click($link->link());
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $request = self::$container->get(HelpRequestRepository::class)->findOneBy(['email' => 'jeanne.martin@example.com']);
        $this->assertNull($request);
    }

    public function testRequest()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/process/j-ai-besoin-d-aide');
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Envoyer ma demande');
        $this->assertCount(1, $button);

        $form = $button->form();
        $form->setValues([
        'composite_help_request[firstName]' => 'Titouan',
        'composite_help_request[lastName]' => 'Galopin',
        'composite_help_request[zipCode]' => 75008,
        'composite_help_request[email]' => 'titouan.galopin@example.com',
        'composite_help_request[jobType]' => 'health',
        'composite_help_request[confirm]' => 1,
        'composite_help_request[c]' => '',
      ]);

        // gets the raw values
        $values = $form->getPhpValues();

        // adds fields to the raw values
        // see https://symfony.com/doc/current/testing.html#adding-and-removing-forms-to-a-collection
        $values['composite_help_request']['details'] = [
            ['helpType' => HelpRequest::TYPE_GROCERIES],
            ['helpType' => HelpRequest::TYPE_BABYSIT, 'childAgeRange' => HelpRequest::AGE_RANGE_35],
            ['helpType' => HelpRequest::TYPE_BABYSIT, 'childAgeRange' => HelpRequest::AGE_RANGE_69],
        ];

        // submits the form with the existing and new values
        $client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        $help_request = self::$container->get(HelpRequestRepository::class)->findOneBy(['email' => 'titouan.galopin@example.com', 'childAgeRange' => HelpRequest::AGE_RANGE_35]);
        $this->assertInstanceOf(HelpRequest::class, $help_request);
        $this->assertSame('Titouan', $help_request->firstName);
        $this->assertSame('Galopin', $help_request->lastName);
        $this->assertSame('75008', $help_request->zipCode);
        $this->assertSame('titouan.galopin@example.com', $help_request->email);
        $this->assertSame('health', $help_request->jobType);
        $this->assertSame(HelpRequest::AGE_RANGE_35, $help_request->childAgeRange);
        $this->assertSame(HelpRequest::TYPE_BABYSIT, $help_request->helpType);

        $help_request = self::$container->get(HelpRequestRepository::class)->findOneBy(['email' => 'titouan.galopin@example.com', 'childAgeRange' => HelpRequest::AGE_RANGE_69]);
        $this->assertInstanceOf(HelpRequest::class, $help_request);
        $this->assertSame('Titouan', $help_request->firstName);
        $this->assertSame('Galopin', $help_request->lastName);
        $this->assertSame('75008', $help_request->zipCode);
        $this->assertSame('titouan.galopin@example.com', $help_request->email);
        $this->assertSame('health', $help_request->jobType);
        $this->assertSame(HelpRequest::AGE_RANGE_69, $help_request->childAgeRange);
        $this->assertSame(HelpRequest::TYPE_BABYSIT, $help_request->helpType);

        $help_request = self::$container->get(HelpRequestRepository::class)->findOneBy(['email' => 'titouan.galopin@example.com', 'helpType' => HelpRequest::TYPE_GROCERIES]);
        $this->assertInstanceOf(HelpRequest::class, $help_request);
    }

    public function testRequestVulnerableSelf()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/process/j-ai-besoin-d-aide-risque');
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Envoyer ma demande');
        $this->assertCount(1, $button);

        $client->submit($button->form(), [
            'vulnerable_help_request[firstName]' => 'Agnès',
            'vulnerable_help_request[lastName]' => 'Jean',
            'vulnerable_help_request[zipCode]' => 92110,
            'vulnerable_help_request[email]' => 'agnes.jean@example.com',
            'vulnerable_help_request[confirm]' => 1,
            'vulnerable_help_request[c]' => '',
        ]);

        $request = self::$container->get(HelpRequestRepository::class)->findOneBy(['email' => 'agnes.jean@example.com']);
        $this->assertInstanceOf(HelpRequest::class, $request);
        $this->assertSame('Agnès', $request->firstName);
        $this->assertSame('Jean', $request->lastName);
        $this->assertSame('92110', $request->zipCode);
        $this->assertSame('agnes.jean@example.com', $request->email);
        $this->assertNull($request->ccEmail);
        $this->assertSame('vulnerable', $request->jobType);
        $this->assertSame(HelpRequest::TYPE_GROCERIES, $request->helpType);
    }

    public function testRequestVulnerableOther()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/process/j-ai-besoin-d-aide-risque');
        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Envoyer ma demande');
        $this->assertCount(1, $button);

        $client->submit($button->form(), [
            'vulnerable_help_request[isCloseOne]' => 1,
            'vulnerable_help_request[ccEmail]' => 'titouan.galopin@example.com',
            'vulnerable_help_request[firstName]' => 'Agnès',
            'vulnerable_help_request[lastName]' => 'Jean',
            'vulnerable_help_request[zipCode]' => 92110,
            'vulnerable_help_request[email]' => 'agnes.jean@example.com',
            'vulnerable_help_request[confirm]' => 1,
            'vulnerable_help_request[c]' => '',
        ]);

        $request = self::$container->get(HelpRequestRepository::class)->findOneBy(['email' => 'agnes.jean@example.com']);
        $this->assertInstanceOf(HelpRequest::class, $request);
        $this->assertSame('Agnès', $request->firstName);
        $this->assertSame('Jean', $request->lastName);
        $this->assertSame('92110', $request->zipCode);
        $this->assertSame('agnes.jean@example.com', $request->email);
        $this->assertSame('titouan.galopin@example.com', $request->ccEmail);
        $this->assertSame('vulnerable', $request->jobType);
        $this->assertSame(HelpRequest::TYPE_GROCERIES, $request->helpType);
    }
}
