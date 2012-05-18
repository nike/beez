<?php

namespace DS\DemoBundle\Command;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;
use DS\DemoBundle\Command\ShellQueue;
use DS\DemoBundle\Command\Helper\DialogHelper;

class Command extends SymfonyCommand
{

  protected $queue;

  protected function configure()
  {
    $this
      ->addArgument('configuration', InputArgument::OPTIONAL, 'Configuration file', '.beez/config.yml')
      ->addOption('force', 'x', InputOption::VALUE_NONE, 'Force execution')
    ;
  }

  public function setQueue($queue)
  {
    $this->queue = $queue;
  }

  protected function getDialogHelper()
  {
    $dialog = $this->getHelperSet()->get('dialog');
    if (!$dialog || get_class($dialog) !== 'DS\\DemoBundle\\ommand\\Helper\\DialogHelper') {
      $this->getHelperSet()->set($dialog = new DialogHelper());
    }

    return $dialog;
  }

  protected function loadConfiguration(InputInterface $input)
  {
    $file = $input->getArgument('configuration');
    if (file_exists($file)) {
      $configuration = Yaml::parse($file);

      foreach ($this->getDefinition()->getOptions() as $option) {
        $name = $option->getName();
        $value = $input->getOption($name);
        if (empty($value) && array_key_exists($name, $configuration)) {
          $input->setOption($name, $configuration[$name]);
        }
      }
    }
  }

}
