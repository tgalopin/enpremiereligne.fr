<?php

namespace App\Tests\Controller;

use App\Entity\Helper;
use App\Repository\HelperRepository;
use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProcessControllerTest extends WebTestCase
{
    public function testHelper()
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
        $this->assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());

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
}
