<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\LastFmBundle\Action;

use Core23\LastFmBundle\Core23LastFmEvents;
use Core23\LastFmBundle\Event\AuthFailedEvent;
use Core23\LastFmBundle\Session\SessionManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

final class AuthErrorAction
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var SessionManager
     */
    private $sessionManager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * AuthErrorAction constructor.
     *
     * @param Environment              $twig
     * @param RouterInterface          $router
     * @param SessionManager           $sessionManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        Environment $twig,
        RouterInterface $router,
        SessionManager $sessionManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->twig            = $twig;
        $this->router          = $router;
        $this->sessionManager  = $sessionManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     *
     * @return Response
     */
    public function __invoke(): Response
    {
        if ($this->sessionManager->isAuthenticated()) {
            return new RedirectResponse($this->generateUrl('core23_lastfm_success'));
        }

        $this->sessionManager->clear();

        $event = new AuthFailedEvent();
        $this->eventDispatcher->dispatch(Core23LastFmEvents::AUTH_ERROR, $event);

        if ($response = $event->getResponse()) {
            return $response;
        }

        return new Response($this->twig->render('@Core23LastFm/Auth/error.html.twig'));
    }

    /**
     * Generates a URL from the given parameters.
     *
     * @param string $route         The name of the route
     * @param array  $parameters    An array of parameters
     * @param int    $referenceType The type of reference (one of the constants in UrlGeneratorInterface)
     *
     * @return string The generated URL
     */
    private function generateUrl($route, array $parameters = [], $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        return $this->router->generate($route, $parameters, $referenceType);
    }
}
