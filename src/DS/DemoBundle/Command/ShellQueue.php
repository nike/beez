<?php

namespace DS\DemoBundle\Command;

//use Symfony\Component\Console\Command\Command;
//use Symfony\Component\Console\Input\InputArgument;
//use Symfony\Component\Console\Input\InputInterface;
//use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class ShellQueue
{

    protected $queue;

    public function __construct()
    {
        $this->queue = array();
    }

    public function run(OutputInterface $output)
    {
        foreach ($this->queue as $command) {
            $process = new Process($command);

            if ($process->run()) {
                $output->writeln(sprintf('<error>%s:</error>', $process->getExitCodeText()));
                $output->writeln('');
                $output->writeln(sprintf('<error>%s</error>', $process->getErrorOutput()));
                return $process->getExitCode();
            } else {
                $output->writeln(sprintf('<info>%s</info>', $process->getExitCodeText()));
            }
        }

        // Ok
        return 0;
    }

    public function addCommandLine($commandLine)
    {
        if (!empty($commandLine))
            $this->queue[] = $commandLine;
    }

    public function printQueue(OutputInterface $output)
    {
        foreach ($this->queue as $item) {
            $output->writeln($item);
        }
    }

}
