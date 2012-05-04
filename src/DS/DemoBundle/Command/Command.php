<?php

namespace DS\DemoBundle\Command;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use DS\DemoBundle\Command\ShellQueue;

class Command extends SymfonyCommand
{

    private $queue;

    public function setQueue($queue)
    {
        $this->queue = $queue;
    }

}
