<?php

/*
 * This file is part of the VinceCms bundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Composer;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\PhpExecutableFinder;
use Composer\Script\CommandEvent;

/**
 * Install JavaScripts files from vendor to public directory
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * @codeCoverageIgnore
 */
class ScriptHandler
{

    /**
     * Install JavaScripts files to public directory (web/js) from vendor
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param CommandEvent $event Command event
     */
    public static function installJavaScripts(CommandEvent $event)
    {
        $options = self::getOptions($event);

        if (!is_dir($options['symfony-app-dir'])) {
            printf(
                'The symfony-app-dir (%s) specified in composer.json was not found in %s, can not build bootstrap '.
                'file.%s',
                $options['symfony-app-dir'],
                getcwd(),
                PHP_EOL
            );

            return;
        }

        if (!is_dir($options['symfony-web-dir'])) {
            printf(
                'The symfony-web-dir (%s) specified in composer.json was not found in %s, can not build bootstrap '.
                'file.%s',
                $options['symfony-web-dir'],
                getcwd(),
                PHP_EOL
            );

            return;
        }

        if (!count($options['javascripts'])) {
            printf('No javascripts to install, please fill `javascripts` option in composer.json, can not build '.
            'bootstrap file.%s', PHP_EOL);

            return;
        }

        $command = sprintf('vince:javascripts:install %s %s', $options['symfony-web-dir'], implode(' ', $options['javascripts']));
        if ($options['symfony-assets-install'] == 'symlink') {
            $command.= ' --symlink';
        }
        static::executeCommand($event, $options['symfony-app-dir'], $command, $options['process-timeout']);
    }

    /**
     * Execute symfony command
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param CommandEvent $event   Command event
     * @param string       $appDir  App directory
     * @param string       $cmd     Command name
     * @param int          $timeout Execution timeout
     *
     * @throws \RuntimeException
     */
    protected static function executeCommand(CommandEvent $event, $appDir, $cmd, $timeout = 300)
    {
        $io  = $event->getIO();
        $php = escapeshellarg(self::getPhp());
        $console = escapeshellarg($appDir.'/console');
        if ($io->isDecorated()) {
            $console .= ' --ansi';
        }

        $process = new Process($php.' '.$console.' '.$cmd, null, null, null, $timeout);
        $process->run(function ($type, $buffer) use ($io, $cmd) {
                if ($type === 'err') {
                    echo $buffer;
                } else {
                    $io->write(sprintf('<info>%s</info>', $buffer));
                }
            }
        );
    }

    /**
     * Get options
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param CommandEvent $event Command event
     *
     * @return array
     */
    protected static function getOptions(CommandEvent $event)
    {
        $options = array_merge(array(
            'symfony-app-dir' => 'app',
            'symfony-web-dir' => 'web',
            'symfony-assets-install' => 'hard',
            'javascripts' => array()
        ), $event->getComposer()->getPackage()->getExtra());

        $options['symfony-assets-install'] = getenv('SYMFONY_ASSETS_INSTALL') ?: $options['symfony-assets-install'];

        $options['process-timeout'] = $event->getComposer()->getConfig()->get('process-timeout');

        return $options;
    }

    /**
     * Get PHP executable
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     * @return string
     * @throws \RuntimeException
     */
    protected static function getPhp()
    {
        $phpFinder = new PhpExecutableFinder;
        if (!$phpPath = $phpFinder->find()) {
            throw new \RuntimeException(
                'The php executable could not be found, add it to your PATH environment variable and try again'
            );
        }

        return $phpPath;
    }
}
