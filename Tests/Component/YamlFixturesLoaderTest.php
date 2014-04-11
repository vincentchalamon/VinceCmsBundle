<?php

/*
 * This file is part of the VinceCms bundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Tests\Component;

use Vince\Bundle\CmsBundle\Component\YamlFixturesLoader;

/**
 * Test YamlFixturesLoader
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class YamlFixturesLoaderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Loader
     *
     * @var YamlFixturesLoader
     */
    protected $loader;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->loader = new YamlFixturesLoader();
    }

    /**
     * Test add invalid file path
     *
     * @expectedException \Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException
     * @expectedExceptionMessage File '/path/to/non/existing/file' not found.
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function testInvalidFilePath()
    {
        $this->loader->addFile('/path/to/non/existing/file');
    }

    /**
     * Test add invalid file extension
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid extension for file 'README.md'.
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function testInvalidFileExtension()
    {
        $this->loader->addFile('README.md');
    }

    /**
     * Test add valid file
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function testValidFile()
    {
        $this->assertEquals($this->loader, $this->loader->addFile('.travis.yml'));
    }

    /**
     * Test add invalid directory path
     *
     * @expectedException \Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException
     * @expectedExceptionMessage Directory '/path/to/non/existing/directory' not found.
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function testInvalidDirectoryPath()
    {
        $this->loader->addDirectory('/path/to/non/existing/directory');
    }

    /**
     * Test add valid directory
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function testValidDirectory()
    {
        $this->assertEquals($this->loader, $this->loader->addDirectory('./', false));
    }

    /**
     * Test load non valid fixtures files
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Class 'language' does not exist in file '.travis.yml'.
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function testLoad()
    {
        $this->loader->addFile('.travis.yml');
        $this->loader->load($this->getMockBuilder('\Doctrine\Common\Persistence\ObjectManager')
                                 ->disableOriginalConstructor()
                                 ->getMock());
    }
}
