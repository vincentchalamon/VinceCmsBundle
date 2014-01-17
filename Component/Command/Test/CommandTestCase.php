<?php

/*
 * This file is part of the VinceCms bundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Component\Command\Test;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Bundle\FrameworkBundle\Client;

/**
 * Base class for testing Symfony2 commands
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
abstract class CommandTestCase extends WebTestCase
{

    /**
     * Runs a command and returns it output
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param Client $client  Client
     * @param string $command Command name
     *
     * @return string Output
     */
    public function runCommand(Client $client, $command)
    {
        $application = new Application($client->getKernel());
        $application->setAutoExit(false);

        $fp     = tmpfile();
        $input  = new StringInput($command);
        $output = new StreamOutput($fp);

        $application->run($input, $output);

        fseek($fp, 0);
        $output = '';
        while (!feof($fp)) {
            $output = fread($fp, 4096);
        }
        fclose($fp);

        return trim($output);
    }
}