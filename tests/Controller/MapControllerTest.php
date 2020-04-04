<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MapControllerTest extends WebTestCase
{
    public function testMapJson()
    {
        $client = static::createClient();
        $client->request('GET', '/map/json');

        $this->assertResponseIsSuccessful();
    }

    public function testMapSvg()
    {
        $client = static::createClient();
        $client->request('GET', '/map/svg');

        $this->assertResponseIsSuccessful();
    }
}
