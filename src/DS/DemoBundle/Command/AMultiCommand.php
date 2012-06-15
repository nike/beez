<?php

namespace DS\DemoBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use DS\DemoBundle\Command\CompositeCommand;

class AMultiCommand extends CompositeCommand
{

  protected function configure()
  {
    $this
      ->setName('exec:a')
      ->addArgument('a-arg', InputArgument::REQUIRED, '')
    ;
  }

  protected function initialize(InputInterface $input, OutputInterface $output)
  {
    $this->addCommandArray(array(
      'command' => 'exec:b',
      'b-arg' => 'b-by-a-'.$input->getArgument('a-arg'),
      ), $output);

    $this->addCommandArray(array(
      'command' => 'exec:c',
      'c-arg' => 'c-by-a-'.$input->getArgument('a-arg'),
      ), $output);

    $this->addCommandArray(array(
      'command' => 'exec:leaf',
      'leaf-arg' => 'l-by-a-'.$input->getArgument('a-arg'),
      ), $output);
  }

}

