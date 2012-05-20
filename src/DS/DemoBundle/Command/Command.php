<?php

namespace DS\DemoBundle\Command;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use DS\DemoBundle\Command\Helper\DialogHelper;

abstract class Command extends BaseCommand
{

  protected $commands = array();

  protected function addConfigurationArgument()
  {
    return $this->addArgument('configuration', InputArgument::OPTIONAL, 'Configuration file', '.beez/config.yml');
  }

  protected function addForceOption()
  {
    return $this->addOption('force', 'x', InputOption::VALUE_NONE, 'Force execution');
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
      $configuration = (array) Yaml::parse($file);

      foreach ($configuration as $key => $value) {
        if ($input->hasOption($key)) {
          if (!$input->getOption($key)) {
            $input->setOption($key, $value);
          }
        }
      }
    }
  }

  protected function addCommand($name, array $args)
  {
    $command = $this->getApplication()->find($name);
    $input = new ArrayInput(array_merge(array($name), $args));

    $this->commands[] = array(
      'command' => $command,
      'input' => $input,
    );
  }

  protected function addCommandLine($commandLine)
  {
    $this->addCommand('shell:execute', array(
//      'command-line' => sprintf('%s', $commandLine),
      'command-line' => $commandLine,
    ));
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $exitCode = 0; //Ok

    foreach ($this->commands as $command) {
      $exitCode = $exitCode || $command['command']->run($command['input'], $output);

      if ($exitCode)
        return $exitCode;
    }
  }

}
