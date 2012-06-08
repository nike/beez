<?php

namespace DS\DemoBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use DS\DemoBundle\Command\Helper\DialogHelper;

abstract class CompositeCommand extends Command
{

  protected $commands = array();
  protected $argumentValidators = array();
  protected $optionValidators = array();

  protected function addConfigurationArgument()
  {
    return $this->addArgument('configuration', InputArgument::OPTIONAL, 'Configuration file', '.beez/config.yml');
  }

  protected function addForceOption()
  {
    return $this->addOption('force', 'x', InputOption::VALUE_NONE, 'Force execution');
  }

  protected function isForced(InputInterface $input)
  {
    if ($input->hasOption('force'))
      return (true === $input->getOption('force'));

    return true;
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

  protected function addCommandArray(array $args, OutputInterface $output)
  {
    $command = $this->getApplication()->find($args['command']);
    $input = new ArrayInput($args);
    $command->initialize($input, $output);

    $this->commands[] = $command;
  }

  protected function addCommandLine($commandLine, OutputInterface $output)
  {
    $array = array(
      'command' => 'shell:execute',
      'command-line' => $commandLine,
    );
    
    $this->addCommandArray($array, $output);
  }
  
  protected function addCommandClosure(\Closure $closure)
  {
    $command = new BaseCommand();
    $command->setCode($closure);
    
    // TODO: must the command be initilized?
    $this->commands[] = $command;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    throw new \LogicException('[CompositeCommand::execute] input is alreay in command...');
    
    $exitCode = 0; //Ok

    foreach ($this->commands as $command) {
      $exitCode = $exitCode || $command['command']->run($command['input'], $output);

      if ($exitCode)
        return $exitCode;
    }
  }

  protected function addArgumentValidators($argument, array $validators)
  {
    $this->argumentValidators[$argument] = $validators;

    return $this;
  }

  protected function addOptionValidators($option, array $validators)
  {
    $this->optionValidators[$option] = $validators;

    return $this;
  }

  protected function validateInput(InputInterface $input)
  {
    foreach ($this->argumentValidators as $argument => $validators) {
      foreach ($validators as $validator) {
        $value = $input->getArgument($argument);
        if (!$validator->validate($value))
          throw new \InvalidArgumentException(sprintf('%s [%s]: %s', $argument, $value, $validator->getErrorMessage()));
      }
    }

    foreach ($this->optionValidators as $option => $validators) {
      foreach ($validators as $validator) {
        $value = $input->getOption($option);
        if (!$validator->validate($value))
          throw new \InvalidArgumentException(sprintf('%s [%s]: %s', $option, $value, $validator->getErrorMessage()));
      }
    }
  }
  
  public function sanitizeDirectory($directory)
  {
    return preg_replace('/\/+$/', '', $directory) . '/';
  }

}