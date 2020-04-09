<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MapControllerTest extends WebTestCase
{
    public function testMapSvg()
    {
        $client = static::createClient();
        $client->request('GET', '/map');

        $this->assertResponseIsSuccessful();
    }
}
