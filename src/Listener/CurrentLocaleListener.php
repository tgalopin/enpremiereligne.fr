<?php

namespace App\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class CurrentLocaleListener implements EventSubscriberInterface
{
    private string $locale;

    public function __construct(string $locale)
    {
        $this->locale = $locale;
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => ['onKernelRequest', 110], // 110: before autodetection of locale
        ];
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $event->getRequest()->setLocale($this->locale);
    }
}
