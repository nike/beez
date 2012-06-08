<?php

namespace DS\DemoBundle\Tests\Command;

use Symfony\Component\Console\Command\Command as BaseCommand;

interface Dryrun
{

  public function dontExecute(InputInterface $input, OutputInterface $output);

}

class CompositeCommand extends BaseCommand
{

  protected $commands = array();

  protected function addCommand(BaseCommand $command)
  {
    $this->commands[] = $command;
  }

  protected function addCommandWithNameAndArgs($name, array $args)
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
    $this->addCommandWithNameAndArgs('shell:execute', array(
      'command-line' => $commandLine,
    ));
  }

  protected function addCommandWithClosure(\Closure $closure)
  {
    $command = new BaseCommand();
    $command->setCode($closure);
    
    $this->commands[] = $command;
  }

  protected function initialize(InputInterface $input, OutputInterface $output)
  {
    foreach ($this->commands as $command) {
      $command->initialize($input, $output);
    }
  }
  
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    foreach ($this->commands as $command) {
      $command->execute($input, $output);
    }
  }

}

class ShellCommand extends BaseCommand
{

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $process = new Process('echo ShellCommand');
    
    if ($process->run()) {
      $output->writeln(sprintf('<error>%s:</error>', $process->getExitCodeText()));
      return $process->getExitCode();
    } else {
      $output->writeln(sprintf('<info>%s</info>', $process->getExitCodeText()));
    }

    return 0;
  }

}


class DatabaseInitCommand extends CompositeCommand
{

  protected function initialize(InputInterface $input, OutputInterface $output)
  {
    $c1 = new ShellCommand();
    $c2 = new ShellCommand();
    
    $this->addCommand($c1);
    $this->addCommand($c2);
    
    parent::initialize($input, $output);
  }

}


class WebsiteDeployCommand extends CompositeCommand
{

  protected function initialize(InputInterface $input, OutputInterface $output)
  {
    $i = $input;
    $o = $output;
    
    $c1 = new ShellCommand();
    $c2 = new DatabaseInitCommand();
    
    $this->addCommand($c1);
    $this->addCommand($c2);
    
    parent::initialize($input, $output);
  }

}
