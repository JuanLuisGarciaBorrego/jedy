<?php

namespace AppBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RedirectToLocaleActiveListener implements EventSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string
     */
    private $localeActive;

    /**
     * @param ContainerInterface $container
     * @param $localeActive
     */
    public function __construct(ContainerInterface $container, $localeActive)
    {
        $this->container = $container;
        $this->localeActive = $localeActive;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if ('/' == $request->getPathInfo()) {
            $route = $this->container->get('router')->generate('app_index', ['_locale' => $this->localeActive]);
            $response = new RedirectResponse($route);
            $event->setResponse($response);
        }

    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

}