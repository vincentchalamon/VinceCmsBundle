<?php

/*
 * This file is part of the VinceType bundle.
 *
 * (c) Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\TypeBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Test Default controller
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class DefaultControllerTest extends WebTestCase
{

    /**
     * Test sitemap
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function testSitemap()
    {
        $client = static::createClient();

        // Method not allowed
        $client->request('POST', '/sitemap.xml');
        $this->assertEquals(405, $client->getResponse()->getStatusCode());

        // File required
        $client->request('GET', '/sitemap.xml');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * Test feed
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function testFeed()
    {
        $client = static::createClient();

        // Method not allowed
        $client->request('POST', '/feed.xml');
        $this->assertEquals(405, $client->getResponse()->getStatusCode());

        // File required
        $client->request('GET', '/feed.xml');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $client->request('GET', '/feed.atom');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $client->request('GET', '/feed.rss');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $client->request('GET', '/feed.html');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $client->request('GET', '/feed.json');
        $this->assertTrue($client->getResponse()->isNotFound());
    }

    /**
     * Test show
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function testShow()
    {
        $client = static::createClient();

        // Method not allowed
        $client->request('DELETE', '/');
        $this->assertEquals(405, $client->getResponse()->getStatusCode());

        // File required
        $client->request('GET', '/feed.xml');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $client->request('GET', '/feed.atom');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $client->request('GET', '/feed.rss');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $client->request('GET', '/feed.html');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $client->request('GET', '/feed.json');
        $this->assertTrue($client->getResponse()->isNotFound());
    }
}
