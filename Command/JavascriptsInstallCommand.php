<?php

/*
 * This file is part of the VinceCms bundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Command;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

/**
 * Install vendor JavaScripts
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class JavascriptsInstallCommand extends DoctrineCommand
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('vince:javascripts:install')
             ->setDescription('This command install JavaScripts files from vendor to public directory')
             ->addArgument('target', InputArgument::REQUIRED, 'Public directory')
             ->addArgument('javascripts', InputArgument::IS_ARRAY, 'JavaScripts relative path (i.e.: `vendor/jquery/jquery/jquery-1.10.2.min.js`')
             ->addOption('symlink', null, InputOption::VALUE_NONE, 'Symlinks the JavaScripts instead of copying it')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filesystem = new Filesystem();
        $origin     = rtrim(realpath($this->getContainer()->getParameter('kernel.root_dir').'/../'), '/');
        $public     = rtrim($input->getArgument('target'), '/');
        $target     = $public.'/js';
        if (!is_dir($target)) {
            $filesystem->mkdir($target, 0777);
        }
        $filesystem->chmod($target, 0777);

        $output->writeln(sprintf('Installing JavaScripts using the <comment>%s</comment> option', $input->getOption('symlink') ? 'symlink' : 'hard copy'));

        foreach ($input->getArgument('javascripts') as $javascript) {
            $filename    = pathinfo($javascript, PATHINFO_BASENAME);
            $source      = $origin.'/'.$javascript;
            $destination = $target.'/'.$filename;
            $output->writeln(sprintf('Installing <comment>%s</comment> JavaScript file into <comment>%s</comment>', $filename, $target));

            // Remove existing file
            if (is_file($destination)) {
                $filesystem->remove($destination);
            }

            // Install JavaScript file
            if ($input->getOption('symlink')) {
                $filesystem->symlink($source, $destination);
            } else {
                $filesystem->copy($source, $destination);
            }
        }
    }

    /**
     * Run Symfony command
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param string          $name    Command name
     * @param OutputInterface $output  Output interface
     * @param array           $options Command options
     */
    protected function runCommand($name, OutputInterface $output, array $options = array())
    {
        $input = new ArrayInput(array_merge(array('command' => $name), $options));
        $input->setInteractive(false);
        $this->getApplication()->find($name)->run($input, $output);
    }

    /**
     * Run shell command
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param string          $name    Command name
     * @param OutputInterface $output  Output interface
     */
    protected function runScript($name, OutputInterface $output)
    {
        $process = new Process($name);
        $process->run(function ($type, $buffer) use ($output) {
                if ($type === 'err') {
                    $output->writeln($buffer);
                } else {
                    $output->writeln(sprintf('<info>%s</info>', $buffer));
                }
            }
        );
    }
}