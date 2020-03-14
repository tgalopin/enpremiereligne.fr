<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class LoginControllerTest extends WebTestCase
{
    public function provideValidLoginCredentials()
    {
        yield ['tgalopin', 'password'];
    }

    /**
     * @dataProvider provideValidLoginCredentials
     */
    public function testLoginValidCredentials(string $username, string $password)
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Se connecter')->form();

        $client->submit($form, ['username' => $username, 'password' => $password]);
        $this->assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect('/admin/'));

        $client->followRedirect();
        $crawler = $client->followRedirect();
        $this->assertCount(1, $crawler->filter('.user-name:contains("'.$username.'")'));
    }

    public function provideInvalidLoginCredentials()
    {
        yield 'invalid_username' => ['invalid', 'password'];
        yield 'empty_username' => ['', 'password'];
        yield 'empty_password' => ['invalid', ''];
        yield 'invalid_password' => ['tgalopin', 'aaa'];
    }

    /**
     * @dataProvider provideInvalidLoginCredentials
     */
    public function testLoginInvalidCredentials(string $email, string $password)
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Se connecter')->form();

        $client->submit($form, ['username' => $email, 'password' => $password]);
        $this->assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
        $crawler = $client->followRedirect();

        $this->assertCount(1, $crawler->filter('button:contains("Se connecter")'));
        $this->assertCount(1, $crawler->filter('.alert-danger'));
    }
}
