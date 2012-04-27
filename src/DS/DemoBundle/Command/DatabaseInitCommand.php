<?php

namespace DS\DemoBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use DS\DemoBundle\Command\ShellQueue;

class DatabaseInitCommand extends Command
{

    private $queue;

    public function setQueue($queue)
    {
        $this->queue = $queue;
    }

    protected function configure()
    {
        $this
          ->setName('database:init')
          ->setDescription('Creates a mysql database and assigns privileges to the specified user')
          ->addArgument('db-name', InputArgument::REQUIRED, 'Database name')
          ->addArgument('db-user', InputArgument::REQUIRED, 'If user does not exist, it will be created')
          ->addArgument('db-pass', InputArgument::REQUIRED, 'Assign or change the password to the specified user')
          ->addOption('mysql-user', 'u', InputOption::VALUE_OPTIONAL, 'Mysql username (if not set, default user is "root")')
          ->addOption('mysql-pass', 'p', InputOption::VALUE_OPTIONAL, 'Mysql password (if not set, default no password)')
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
        $this->queue->addCommandLine($commandLine);

        $sql = sprintf('grant all privileges on %s.* to \'%s\'@\'localhost\' identified by \'%s\'', $dbName, $dbUser, $dbPass);
        $commandLine = sprintf('mysql %s %s -e "%s"', $mysqlUser, $mysqlPass, $sql);
        $this->queue->addCommandLine($commandLine);

        $dialog = $this->getHelperSet()->get('dialog');
        if (!$dialog->askConfirmation($output, '<question>Continue with this action?</question>', false)) {
            return;
        }

        return $this->queue->run($output);
    }

}
