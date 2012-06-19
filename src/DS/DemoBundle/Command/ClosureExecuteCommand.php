<?php

namespace DS\DemoBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ClosureExecuteCommand extends Command
{

  protected function configure()
  {
    $this
      ->setName('closure:execute')
      ->setDescription('Execute a closure')
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    throw new \LogicException('You must call setCode()');
  }
  
}
