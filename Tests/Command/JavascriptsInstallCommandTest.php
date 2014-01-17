<?php

/*
 * This file is part of the VinceCms bundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Tests\Command;

use Vince\Bundle\CmsBundle\Component\Command\Test\CommandTestCase;

/**
 * Test vince:javascripts:install command
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class JavascriptsInstallCommandTest extends CommandTestCase
{

    /**
     * Test command with default options
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     */
    public function testDefault()
    {
        // Build client
        $client = static::createClient();

        // Test command
        $output = $this->runCommand($client, 'vince:javascripts:install web vendor/jquery/jquery/jquery-1.10.2.min.js --symlink');
        $this->assertContains('Installing JavaScripts using the symlink option.', $output);
        $this->assertContains('Installing jquery-1.10.2.min.js JavaScript file into web/js/.', $output);
        $this->assertFileExists($client->getContainer()->getParameter('kernel.root_dir').'/../web/js/jquery-1.10.2.min.js');
    }
}