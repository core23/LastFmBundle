<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\LastFmBundle\Tests\Event;

use Nucleos\LastFmBundle\Event\AuthFailedEvent;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\HttpFoundation\Response;

final class AuthFailedEventTest extends TestCase
{
    use ProphecyTrait;

    public function testGetResponse(): void
    {
        $event = new AuthFailedEvent();

        static::assertNull($event->getResponse());
    }

    public function testSetResponse(): void
    {
        $reponse = $this->prophesize(Response::class);

        $event = new AuthFailedEvent();
        $event->setResponse($reponse->reveal());

        static::assertSame($reponse->reveal(), $event->getResponse());
    }
}
