<?php

/*
 * This file is part of the VinceCms bundle.
 *
 * (c) Vincent Chalamon <http://www.vincent-chalamon.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Vince\Bundle\CmsBundle\Component\YamlFixturesLoader as Loader;

/**
 * Load CMS default fixtures from yml
 *
 * @author Vincent CHALAMON <vincentchalamon@gmail.com>
 */
class CmsData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load fixtures files
     *
     * @author Vincent CHALAMON <vincentchalamon@gmail.com>
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $loader = new Loader();
        $loader->addFile(__DIR__.'/../../Resources/config/data/Metas.yml');
        $loader->load($manager, null, $this);
    }

    public function getOrder()
    {
        return 1;
    }
}
