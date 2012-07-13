<?php

namespace DS\DemoBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use DS\DemoBundle\Command\CompositeCommand;
use DS\DemoBundle\Command\Validator\DirectoryExists;

class DatabaseDumpCommand extends CompositeCommand
{

    protected function configure()
    {
        $this
            ->setName('database:dump')
            ->setDescription('Dump a mysql database')
            ->addArgument('destination', InputArgument::REQUIRED, 'Directory where to put the dump file')
            ->addArgument('db-name', InputArgument::REQUIRED, 'Database name')
            ->addArgument('db-user', InputArgument::REQUIRED, 'Database user')
            ->addArgument('db-pass', InputArgument::OPTIONAL, 'Database password')
        ;

        $this
            ->addArgumentValidators('destination', array(new DirectoryExists()))
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->validateInput($input);

        $destination = realpath($input->getArgument('destination'));

        $dbName = $input->getArgument('db-name');
        $dbUser = $input->getArgument('db-user');
        $dbPass = $input->getArgument('db-pass') ? sprintf('--password=%s', $input->getArgument('db-pass')) : '';

        $fileName = sprintf('%s/%s-%s.sql', $destination, $dbName, date('YmdHi', time()));

        $commandLine = sprintf('mysqldump --user=%s %s %s > %s', $dbUser, $dbPass, $dbName, $fileName);

        $this->addCommandLine($commandLine, $output);
    }

}
