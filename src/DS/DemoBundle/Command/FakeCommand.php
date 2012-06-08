<?php

namespace DS\DemoBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FakeCommand extends Command
{

  protected function configure()
  {
    $this
      ->setName('fake:result')
      ->setDescription('Fake command')
      ->addArgument('result', InputArgument::OPTIONAL, 'Fail or pass the command (possible values: [pass, fail])')
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $result = $input->getArgument('result');

    if ($result == 'fail') {
      $output->writeln(sprintf('<error>%s</error>', 'FAIL'));
      
      return 1;
    }

    $output->writeln(sprintf('<info>%s</info>', 'PASS'));
    
    return 0;
  }

}
