<?php

namespace App\Tests;

use App\Repository\AdminRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;

abstract class WebTestCase extends BaseWebTestCase
{
    protected function authenticate(KernelBrowser $browser, string $username)
    {
        $user = self::$container->get(AdminRepository::class)->findOneBy(['username' => $username]);
        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());

        /** @var Request|null $request */
        $request = self::$container->get(RequestStack::class)->getCurrentRequest();
        if ($request) {
            self::$container->get(SessionAuthenticationStrategyInterface::class)->onAuthentication($request, $token);
        }

        self::$container->get(TokenStorageInterface::class)->setToken($token);

        $session = self::$container->get(SessionInterface::class);
        $session->set('_security_main', serialize($token));
        $session->save();

        $browser->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));
    }

    protected function logout(KernelBrowser $browser)
    {
        $browser->getCookieJar()->clear();
    }
}
