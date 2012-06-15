<?php

namespace DS\DemoBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use DS\DemoBundle\Command\CompositeCommand;

class LeafCommand extends Command
{

  protected function configure()
  {
    $this
      ->setName('exec:leaf')
      ->addArgument('leaf-arg', InputArgument::REQUIRED, '')
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $output->writeln($input->getArgument('leaf-arg'));
    return 0;
  }

}
