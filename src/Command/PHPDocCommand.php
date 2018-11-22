<?php

/*
 * This file is part of the Moodle Plugin CI package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Copyright (c) 2018 Blackboard Inc. (http://www.blackboard.com)
 * License http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace MoodlePluginCI\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\ProcessBuilder;

class PHPDocCommand extends AbstractPluginCommand
{
    use ExecuteTrait;

    protected function configure()
    {
        parent::configure();

        $this->setName('phpdoc')
            ->setDescription('Run Moodle PHPDoc Checker on a plugin');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        $this->initializeExecute($output, $this->getHelper('process'));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->outputHeading($output, 'Moodle PHPDoc Checker on %s');

        $process = $this->execute->passThroughProcess(
            ProcessBuilder::create()
                ->setPrefix('php')
                ->add('local/moodlecheck/cli/moodlecheck.php')
                ->add('-p=' . $this->plugin->directory)
                ->add('-f=text')
                ->setTimeout(null)
                ->setWorkingDirectory($this->moodle->directory)
                ->getProcess()
        );

        // moodlecheck.php does not return valid exit status,
        // We have to parse output to see if there are errors.
        $results = $process->getOutput();
        return (preg_match('/\s+Line/', $results)) ? 1 : 0;
    }
}
