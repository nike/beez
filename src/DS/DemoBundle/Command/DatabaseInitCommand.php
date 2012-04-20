<?php

namespace DS\DemoBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class DatabaseInitCommand extends Command
{
    protected $queue;
    
    protected function configure()
    {
        $this
          ->setName('database:init')
          ->setDescription('Creates a mysql database and assigns privileges to the specified user')
          ->addArgument('db-name', InputArgument::REQUIRED, 'Database name')
          ->addOption('db-user', '', InputOption::VALUE_REQUIRED, 'If user does not exist, it will be created')
          ->addOption('db-pass', '', InputOption::VALUE_REQUIRED, 'Assign or change the password to the specified user')
          ->addOption('mysql-user', 'u', InputOption::VALUE_OPTIONAL, 'Mysql username (if not set, default user is "root")')
          ->addOption('mysql-pass', 'p', InputOption::VALUE_OPTIONAL, 'Mysql password (if not set, default no password)')
        ;
        
        $this->queue = array();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dbName = $input->getArgument('db-name');
        $dbUser = $input->getOption('db-user');
        $dbPass = $input->getOption('db-pass');
        $mysqlUser = $input->getOption('mysql-user') ? sprintf('-u%s', $input->getOption('mysql-user')) : '-uroot';
        $mysqlPass = $input->getOption('mysql-pass') ? sprintf('-p%s', $input->getOption('mysql-pass')) : '';

        $commandLine = sprintf('mysqladmin %s %s create %s', $mysqlUser, $mysqlPass, $dbName);
        
        $this->queue[] = $commandLine;

//        $process = new Process($commandLine);
//
//        if ($process->run()) {
//            $output->writeln(sprintf('<error>%s:</error>', $process->getExitCodeText()));
//            $output->writeln('');
//            $output->writeln(sprintf('<error>%s</error>', $process->getErrorOutput()));
//            return $process->getExitCode();
//        } else {
//            $output->writeln(sprintf('<info>%s</info>', $process->getErrorOutput()));
//            $output->writeln(sprintf('<info>%s</info>', $process->getExitCodeText()));
//        }

        if ($dbUser) {
            $sql = sprintf('grant all privileges on %s.* to \'%s\'@\'localhost\'', $dbName, $dbUser);

            if ($dbPass) {
                $sql = $sql . sprintf(' identified by \'%s\'', $dbPass);
            }

            $commandLine = sprintf('mysql %s %s -e "%s"', $mysqlUser, $mysqlPass, $sql);
            
            $this->queue[] = $commandLine;

//            $process = new Process($commandLine);
//
//            if ($process->run()) {
//                $output->writeln(sprintf('<error>%s:</error>', $process->getExitCodeText()));
//                $output->writeln('');
//                $output->writeln(sprintf('<error>%s</error>', $process->getErrorOutput()));
//                return $process->getExitCode();
//            } else {
//                $output->writeln(sprintf('<info>%s</info>', $process->getErrorOutput()));
//                $output->writeln(sprintf('<info>%s</info>', $process->getExitCodeText()));
//            }
        }

        // Ok
        return 0;
    }

}
