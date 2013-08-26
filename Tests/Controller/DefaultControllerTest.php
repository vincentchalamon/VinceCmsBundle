<?php

namespace Vince\Bundle\CmsBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{

    public function testHomepage()
    {
        $this->loadFixtures(array('Vince\Bundle\CmsBundle\Tests\Fixtures\CmsData'));

        // started_at <= now()
        // ended_at is null
        // url is '/'
        // getRoutePattern is homepage
        $client = $this->createClient();
        $crawler = $client->request('GET', '/users/foo');

        $this->assertTrue($crawler->filter('html:contains("Email: foo@bar.com")')->count() > 0);
    }
}
