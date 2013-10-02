<?php

/*
 * This file is part of the VinceCmsBundle.
 *
 * (c) Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Tests\Entity\Repository;

use FOS\ElasticaBundle\Finder\TransformedFinder;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Vince\Bundle\CmsBundle\Entity\Repository\ArticleRepository;

class ArticleRepositoryTest extends WebTestCase
{

    /** @var $repository ArticleRepository */
    protected $repository;

    /** @var $finder TransformedFinder */
    protected $finder;

    /**
     * Load test fixtures
     *
     * {@inheritdoc}
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function setUp()
    {
        $client           = static::createClient();
        $container        = $client->getContainer();
        $this->repository = $container->get('doctrine.orm.entity_manager')->getRepository('VinceCmsBundle:Article');
        $this->finder     = $container->get('fos_elastica.finder.website.article');

        // Load test fixtures
        $this->loadFixtures(array('Vince\Bundle\CmsBundle\Tests\DataFixtures\CmsData'));
    }

    /**
     * Homepage should not rises in search
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function testHomepage()
    {
        $this->assertEquals(0, count($this->finder->find($this->repository->createSearchQuery('homepage'))));
        $this->assertEquals(0, count($this->finder->find($this->repository->createSearchQuery('accueil'))));
    }

    /**
     * Search page should not rises in search
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function testSearch()
    {
        $this->assertEquals(0, count($this->finder->find($this->repository->createSearchQuery('search'))));
        $this->assertEquals(0, count($this->finder->find($this->repository->createSearchQuery('rechercher'))));
    }

    /**
     * Non published pages should not rises in search
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function testNonPublished()
    {
        $this->assertEquals(0, count($this->finder->find($this->repository->createSearchQuery('vincent'))));
    }

    /**
     * Pre published pages should not rises in search
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function testPrePublished()
    {
        $this->assertEquals(0, count($this->finder->find($this->repository->createSearchQuery('jordan'))));
    }

    /**
     * Pre published temp pages should not rises in search
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function testPrePublishedTemp()
    {
        $this->assertEquals(0, count($this->finder->find($this->repository->createSearchQuery('samuel'))));
    }

    /**
     * Unpublished pages should not rises in search
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function testUnpublished()
    {
        $this->assertEquals(0, count($this->finder->find($this->repository->createSearchQuery('franck'))));
    }

    /**
     * Published pages should rises in search
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function testPublished()
    {
        $this->assertEquals(1, count($this->finder->find($this->repository->createSearchQuery('yannick'))));
    }

    /**
     * Published today pages should rises in search
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function testPublishedToday()
    {
        $this->assertEquals(1, count($this->finder->find($this->repository->createSearchQuery('benoit'))));
    }

    /**
     * Published until today pages should rises in search
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function testPublishedUntilToday()
    {
        $this->assertEquals(1, count($this->finder->find($this->repository->createSearchQuery('gilles'))));
    }

    /**
     * Published temp pages should rises in search
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function testPublishedTemp()
    {
        $this->assertEquals(1, count($this->finder->find($this->repository->createSearchQuery('adrien'))));
    }
}
