<?php

namespace DS\DemoBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use DS\DemoBundle\Command\Command;
use DS\DemoBundle\Command\ShellQueue;

class WebsiteInitCommand extends Command
{

  protected function configure()
  {
    $this
      ->setName('website:init')
      ->setDescription(<<<TXT
Examples:

  Enables plugins required by <info>website:deploy</info>
  <info>./beez website:init</info>
  
  Creates the deploy dir and the web dir (if they do not exist)
  <info>./beez website:init /var/www/website.com</info>

  Creates the database and the user (mysql user and pass are usually required)
  Database will be created ONLY if <comment>--db-name</comment>, <comment>--db-user</comment>, <comment>--db-pass</comment> options are specified
  <info>./beez website:init --db-name=dbname --db-user=dbuser --db-pass=dbPaZZ --mysql-user=root --mysql-pass=Pa55</info>
  
  Populates the database (use mysql user and pass)
  <info>./beez website:init --db-name=dbname --db-dump-file=/my/project/db/dump.sql --mysql-user=root --mysql-pass=Pa55</info>
TXT
    );
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    return 0;
  }

  protected function interact(InputInterface $input, OutputInterface $output)
  {
    
  }

}
