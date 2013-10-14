<?php

/*
 * This file is part of the VinceCmsBundle.
 *
 * (c) Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace My\Bundle\CmsBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{

    public function setUp()
    {
        $this->loadFixtures(array('Vince\Bundle\CmsBundle\Tests\DataFixtures\CmsData'));
    }

    /**
     * Test sitemap results
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function testSitemap()
    {
        // Order by startedAt desc
        // Check presence of nodes
        // Only published pages
    }

    /**
     * Test RSS feed results
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function testFeed()
    {
        // Test formats : rss/xml, html
        // Check presence of nodes (xml)
        // Only published pages
    }

    /**
     * Test page rendering
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function testShow()
    {}
}
