<?php

/*
 * This file is part of the VinceCmsBundle.
 *
 * (c) Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Tests\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Vince\Bundle\CmsBundle\Lib\YamlFixturesLoader as Loader;

/**
 * Load fixtures from yml for tests
 *
 * @author Vincent CHALAMON <vincentchalamon@gmail.com>
 */
class CmsData extends AbstractFixture
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
        $loader->addDirectory(__DIR__.'/../../Resources/config/data');
        $loader->addDirectory(__DIR__.'/../../Resources/config/data-test');
        $loader->load($manager, null, $this);
    }
}