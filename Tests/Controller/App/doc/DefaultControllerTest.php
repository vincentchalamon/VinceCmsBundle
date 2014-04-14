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

        // Successful
        $client->request('GET', '/sitemap.xml');
        $this->assertTrue($client->getResponse()->isOk());
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
        $client->request('POST', '/rss.xml');
        $this->assertEquals(405, $client->getResponse()->getStatusCode());

        // Successful
        $client->request('GET', '/rss.xml');
        $this->assertTrue($client->getResponse()->isOk());
        $client->request('GET', '/rss.atom');
        $this->assertTrue($client->getResponse()->isOk());
        $client->request('GET', '/rss.rss');
        $this->assertTrue($client->getResponse()->isOk());
        $client->request('GET', '/rss.html');
        $this->assertTrue($client->getResponse()->isOk());
        $client->request('GET', '/rss.json');
        $this->assertTrue($client->getResponse()->isNotFound());
    }
}
