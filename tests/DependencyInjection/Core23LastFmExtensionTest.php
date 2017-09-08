<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\LastFmBundle\Tests\DependencyInjection;

use Core23\LastFmBundle\DependencyInjection\Core23LastFmExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

class Core23LastFmExtensionTest extends AbstractExtensionTestCase
{
    public function testLoadDefault()
    {
        $this->load(array(
            'api' => array(
                'app_id'        => 'foo_id',
                'shared_secret' => 'bar_secret',
            ),
        ));

        $this->assertContainerBuilderHasParameter('core23.lastfm.auth_success.redirect_route');
        $this->assertContainerBuilderHasParameter('core23.lastfm.auth_success.redirect_route_params', array());
        $this->assertContainerBuilderHasParameter('core23.lastfm.auth_error.redirect_route');
        $this->assertContainerBuilderHasParameter('core23.lastfm.auth_error.redirect_route_params', array());

        $this->assertContainerBuilderHasParameter('core23.lastfm.api.app_id', 'foo_id');
        $this->assertContainerBuilderHasParameter('core23.lastfm.api.shared_secret', 'bar_secret');
        $this->assertContainerBuilderHasParameter('core23.lastfm.api.endpoint', 'http://ws.audioscrobbler.com/2.0/');
        $this->assertContainerBuilderHasParameter('core23.lastfm.api.auth_url', 'http://www.last.fm/api/auth/');

        $this->assertContainerBuilderHasAlias('core23.lastfm.http.client', 'httplug.client.default');
        $this->assertContainerBuilderHasAlias('core23.lastfm.http.message_factory', 'httplug.message_factory.default');
    }

    protected function getContainerExtensions()
    {
        return array(
            new Core23LastFmExtension(),
        );
    }
}