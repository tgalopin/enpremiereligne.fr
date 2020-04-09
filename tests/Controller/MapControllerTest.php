<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MapControllerTest extends WebTestCase
{
    public function testNeedsMap()
    {
        $client = static::createClient();
        $client->request('GET', '/needs-map');

        $this->assertResponseIsSuccessful();
    }
}
