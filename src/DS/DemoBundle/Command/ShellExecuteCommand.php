<?php

namespace DS\DemoBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class ShellExecuteCommand extends Command
{

    private $forced = true;

    public function setForced($force)
    {
        $this->forced = $force;
    }

    protected function configure()
    {
        $this
            ->setName('shell:execute')
            ->setDescription('Execute a shell command line')
            ->addArgument('command-line', InputArgument::REQUIRED, 'Command line to execute')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commandLine = $input->getArgument('command-line');

        if ($this->forced) {
            $process = new Process($commandLine);

            if ($process->run()) {
                $output->writeln(sprintf('<error>%s:</error>', $process->getExitCodeText()));
                $output->writeln('');
                $output->writeln(sprintf('<error>%s</error>', $process->getErrorOutput()));
                return $process->getExitCode();
            } else {
                $output->writeln(sprintf('<info>%s</info>', $process->getExitCodeText()));
            }
        } else {
            $output->writeln($commandLine);
        }

        // Ok
        return 0;
    }

}
