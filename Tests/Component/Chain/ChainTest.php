<?php

/*
 * This file is part of the VinceCms bundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Tests\Component\Chain;

use Vince\Bundle\CmsBundle\Component\Chain\Chain;

/**
 * Test Chain
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class ChainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test methods
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function testMethods()
    {
        $chain = new Chain();
        $this->assertFalse($chain->has('foo'));
        $chain->add('bar', 'foo');
        $this->assertTrue($chain->has('foo'));
        $this->assertEquals('bar', $chain->get('foo'));
    }
}
