<?php

namespace DS\DemoBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use DS\DemoBundle\Command\CompositeCommand;

class BMultiCommand extends CompositeCommand
{

  protected function configure()
  {
    $this
      ->setName('exec:b')
      ->addArgument('b-arg', InputArgument::REQUIRED, '')
    ;
  }

  protected function initialize(InputInterface $input, OutputInterface $output)
  {
    $this->addCommandArray(array(
      'command' => 'exec:c',
      'c-arg' => 'c-by-b-' . $input->getArgument('b-arg'),
      ), $output);

    $this->addCommandArray(array(
      'command' => 'exec:leaf',
      'leaf-arg' => 'l-by-b-' . $input->getArgument('b-arg'),
      ), $output);
  }

}
