<?php

namespace Src\EventListener;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ResponseEvent;


final class AddHeaderToRequestListener
{

    public function __construct(
        #[Autowire('%env(DEVELOPER_NAME)%')] private string $developerName
    )
    { }

    #[AsEventListener]
    public function onResponseEvent(ResponseEvent $event): void
    {
        $event->getResponse()->headers->set('X-Developer', $this->developerName);
        dump($event->getResponse()->headers);
    }
}
