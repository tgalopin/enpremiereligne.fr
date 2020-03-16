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
}
