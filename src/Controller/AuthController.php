<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\LastFmBundle\Controller;

use Core23\LastFm\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class AuthController extends Controller
{
    public const SESSION_LASTFM_NAME  = '_CORE23_LASTFM_NAME';
    public const SESSION_LASTFM_TOKEN = '_CORE23_LASTFM_TOKEN';

    /**
     * @return Response
     */
    public function authAction(): Response
    {
        $callbackUrl = $this->generateUrl('core23_lastfm_check', [], UrlGeneratorInterface::ABSOLUTE_URL);

        return $this->redirect($this->getAuthService()->getAuthUrl($callbackUrl));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function checkAction(Request $request): Response
    {
        $token = $request->query->get('token');

        if (!$token) {
            return $this->redirectToRoute('core23_lastfm_auth');
        }

        // Store session
        $lastFmSession = $this->getAuthService()->createSession($token);

        if (null === $lastFmSession) {
            return $this->redirectToRoute('core23_lastfm_error');
        }

        /** @var Session $session */
        $session = $this->getSession();
        $session->set(static::SESSION_LASTFM_NAME, $lastFmSession->getName());
        $session->set(static::SESSION_LASTFM_TOKEN, $lastFmSession->getKey());

        return $this->redirectToRoute('core23_lastfm_success');
    }

    /**
     * @return Response
     */
    public function errorAction(): Response
    {
        if ($this->isAuthenticated()) {
            return $this->redirectToRoute('core23_lastfm_success');
        }

        if (null !== $this->getParameter('core23.lastfm.auth_error.redirect_route')) {
            return $this->redirectToRoute($this->getParameter('core23.lastfm.auth_error.redirect_route'), $this->getParameter('core23.lastfm.auth_error.redirect_route_params'));
        }

        return $this->render('Core23LastFmBundle:Auth:error.html.twig');
    }

    /**
     * @return Response
     */
    public function successAction(): Response
    {
        if (!$this->isAuthenticated()) {
            return $this->redirectToRoute('core23_lastfm_error');
        }

        if (null !== $this->getParameter('core23.lastfm.auth_success.redirect_route')) {
            return $this->redirectToRoute($this->getParameter('core23.lastfm.auth_success.redirect_route'), $this->getParameter('core23.lastfm.auth_success.redirect_route_params'));
        }

        $session = $this->getSession();

        return $this->render('Core23LastFmBundle:Auth:success.html.twig', [
            'name' => $session->get(static::SESSION_LASTFM_NAME),
        ]);
    }

    /**
     * Returns the auth status.
     *
     * @return bool
     */
    private function isAuthenticated(): bool
    {
        return (bool) $this->getSession()->get(static::SESSION_LASTFM_TOKEN);
    }

    /**
     * @return AuthService
     */
    private function getAuthService(): AuthService
    {
        return $this->get('core23.lastfm.service.auth');
    }

    /**
     * @return SessionInterface
     */
    private function getSession(): SessionInterface
    {
        return $this->get('session');
    }
}
