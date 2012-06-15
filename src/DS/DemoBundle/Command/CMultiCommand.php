<?php

namespace DS\DemoBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use DS\DemoBundle\Command\CompositeCommand;

class CMultiCommand extends CompositeCommand
{

  protected function configure()
  {
    $this
      ->setName('exec:c')
      ->addArgument('c-arg', InputArgument::REQUIRED, '')
    ;
  }

  protected function initialize(InputInterface $input, OutputInterface $output)
  {
    $this->addCommandArray(array(
      'command' => 'exec:leaf',
      'leaf-arg' => 'l-by-c-'.$input->getArgument('c-arg'),
      ), $output);

    $this->addCommandArray(array(
      'command' => 'exec:leaf',
      'leaf-arg' => 'l-by-c-'.$input->getArgument('c-arg'),
      ), $output);
  }

}
