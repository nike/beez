<?php

namespace DS\DemoBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use DS\DemoBundle\Command\Command;

class DatabaseInitCommand extends Command
{

  protected function configure()
  {
    $this
      ->setName('database:init')
      ->setDescription('Create a mysql database and assign privileges to the specified user')
      ->addArgument('db-name', InputArgument::REQUIRED, 'Database name')
      ->addArgument('db-user', InputArgument::REQUIRED, 'If user does not exist, it will be created')
      ->addArgument('db-pass', InputArgument::REQUIRED, 'Assign or change the password to the specified user')
      ->addOption('mysql-user', 'u', InputOption::VALUE_OPTIONAL, 'Mysql username (if not set, default user is "root")')
      ->addOption('mysql-pass', 'p', InputOption::VALUE_OPTIONAL, 'Mysql password (if not set, default no password)')
      ->addForceOption()
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $dbName = $input->getArgument('db-name');
    $dbUser = $input->getArgument('db-user');
    $dbPass = $input->getArgument('db-pass');
    $mysqlUser = $input->getOption('mysql-user') ? sprintf('-u %s', $input->getOption('mysql-user')) : '-u root';
    $mysqlPass = $input->getOption('mysql-pass') ? sprintf('-p %s', $input->getOption('mysql-pass')) : '';

    $commandLine = sprintf('mysqladmin %s %s create %s', $mysqlUser, $mysqlPass, $dbName);
    $this->addShellCommand($commandLine);

    $sql = sprintf('grant all privileges on %s.* to \'%s\'@\'localhost\' identified by \'%s\'', $dbName, $dbUser, $dbPass);
    $commandLine = sprintf('mysql %s %s -e "%s"', $mysqlUser, $mysqlPass, $sql);
    $this->addShellCommand($commandLine);

    if (!$this->isForced($input)) {
      $output->writeln(sprintf(
        '<info>%s</info> %s %s %s --mysql-user=%s --mysql-pass=%s',
        $this->getName(), $dbName, $dbUser, $dbPass, $mysqlUser, $mysqlPass
      ));
      
      return 0;
    }
    
    return $this->executeCommands($output);
  }

  protected function interact(InputInterface $input, OutputInterface $output)
  {
    return;

    $dialog = $this->getDialogHelper();

    $arguments = array(
      $this->getDefinition()->getArgument('db-name'),
      $this->getDefinition()->getArgument('db-user'),
      $this->getDefinition()->getArgument('db-pass'),
    );

    $options = array(
      $this->getDefinition()->getOption('mysql-user'),
      $this->getDefinition()->getOption('mysql-pass'),
    );

    foreach ($arguments as $argument) {
      $name = $argument->getName();
      $value = $input->getArgument($name);

      if (!$value)
        $value = $argument->getDefault();

      $value = $dialog->ask($output, $dialog->getQuestion($name, $value), $value);

      if ($value)
        $input->setArgument($name, $value);
    }

    foreach ($options as $option) {
      $name = $option->getName();
      $value = $input->getOption($name);

      if (!$value)
        $value = $option->getDefault();

      $value = $dialog->ask($output, $dialog->getQuestion($name, $value), $value);
      $input->setOption($name, $value);
    }
  }

}
