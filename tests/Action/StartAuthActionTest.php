<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\LastFmBundle\Tests\Action;

use Nucleos\LastFm\Service\AuthServiceInterface;
use Nucleos\LastFmBundle\Action\StartAuthAction;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final class StartAuthActionTest extends TestCase
{
    use ProphecyTrait;

    private $authService;

    private $router;

    protected function setUp(): void
    {
        $this->authService = $this->prophesize(AuthServiceInterface::class);
        $this->router      = $this->prophesize(RouterInterface::class);
    }

    public function testExecute(): void
    {
        $this->router->generate('nucleos_lastfm_check', [], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn('/start')
        ;

        $this->authService->getAuthUrl('/start')
            ->willReturn('https://lastFm/login')
        ;

        $action = new StartAuthAction(
            $this->authService->reveal(),
            $this->router->reveal()
        );

        static::assertSame('https://lastFm/login', $action()->getTargetUrl());
    }
}
