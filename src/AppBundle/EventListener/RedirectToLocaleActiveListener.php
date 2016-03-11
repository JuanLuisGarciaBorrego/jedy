<?php

namespace AppBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class RedirectToLocaleActiveListener
 * When a user enters to the homepage without the parameter locale,
 * the subscriber redirects the user to the main locale.
 *
 * @package AppBundle\EventListener
 */
class RedirectToLocaleActiveListener implements EventSubscriberInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var string
     */
    private $localeActive;

    /**
     * @param UrlGeneratorInterface $urlGenerator
     * @param $localeActive
     */
    public function __construct(UrlGeneratorInterface $urlGenerator, $localeActive)
    {
        $this->urlGenerator = $urlGenerator;
        $this->localeActive = $localeActive;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if ('/' == $request->getPathInfo()) {
            $route = $this->urlGenerator->generate('app_index', ['_locale' => $this->localeActive]);

            $response = new RedirectResponse($route);
            $event->setResponse($response);
        }
    }
}
