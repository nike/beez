<?php

namespace DS\DemoBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;
use DS\DemoBundle\Command\Command;
use DS\DemoBundle\Command\ShellQueue;

class WebsiteDeployCommand extends Command
{

  protected function configure()
  {
    parent::configure();

    $this
      ->setName('website:deploy')
      ->setDescription('Deploy a website')
      ->addOption('website-name', '', InputOption::VALUE_REQUIRED, 'Website name')
      ->addOption('web-source-dir', '', InputOption::VALUE_REQUIRED, 'Website source directory')
      ->addOption('web-prod-dir', '', InputOption::VALUE_REQUIRED, 'Website production directory')
      ->addOption('web-user', '', InputOption::VALUE_REQUIRED, 'Website user')
      ->addOption('exclude-file', '', InputOption::VALUE_REQUIRED, 'Exclude file')
      ->addOption('include-file', '', InputOption::VALUE_REQUIRED, 'Include file')
      ->addOption('backup-sources', '', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Backup sources')
      ->addOption('backup-destination', '', InputOption::VALUE_REQUIRED, 'Backup destination')
      ->addOption('db-name', '', InputOption::VALUE_REQUIRED, 'Database name')
      ->addOption('db-user', '', InputOption::VALUE_REQUIRED, 'Database user')
      ->addOption('db-pass', '', InputOption::VALUE_REQUIRED, 'Database pass')
      ->addOption('init', 'i', InputOption::VALUE_NONE, 'First deploy')
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
//    $force = $input->getOption('force');
    // Load configuration

    $this->loadConfiguration($input);

    // Variables from config
    $websiteName = $input->getOption('website-name');
    $excludeList = $input->getOption('exclude-file');
    $includeList = $input->getOption('include-file');
    //    $deployDir = $configuration['deploy_dir'];
    $backupSources = $input->getOption('backup-sources');
    $backupDestination = $input->getOption('backup-destination');
    $webSourceDir = $input->getOption('web-source-dir');
    $webProdDir = $input->getOption('web-prod-dir');
    $webUser = $input->getOption('web-user');
    //    $webGroup = $configuration['web_group'];
    $dbName = $input->getOption('db-name');
    $dbUser = $input->getOption('db-user');
    $dbPass = $input->getOption('db-pass');

    // Backup
    if (!empty($backupSources) && !empty($backupDestination)) {
      $command = $this->getApplication()->find('filesystem:backup');

      $commandInput = new ArrayInput(array(
        'source' => $backupSources[0],
        'destination' => $backupDestination,
      ));
    }
    
    if ($input->getOption('force')) {
      
    } else {
      $this->queue->printQueue($output);
    }
    
    //    // Dump database
    //    if ($dbName && $dbUser && $dbPass) {
    //      $cmd = new nbMysqlDumpCommand();
    //      $cmdLine = sprintf('%s %s %s %s', $dbName, $backupDestination, $dbUser, $dbPass);
    //      $this->executeCommand($cmd, $cmdLine, $force, $verbose);
    //    }
    //
    //    // Sync web directory
    //    $cmd = new nbDirTransferCommand();
    //    $delete = isset($options['delete']) ? '--delete' : '';
    //    $cmdLine = sprintf('%s %s --owner=%s --exclude-from=%s --include-from=%s %s %s',
    //      $webSourceDir,
    //      $webProdDir,
    //      $webUser,
    //      $excludeList,
    //      $includeList,
    //      $force ? '--doit' : '',
    //      $delete
    //    );
    //    $this->executeCommand($cmd, $cmdLine, true, $verbose);
  }

  protected function interact(InputInterface $input, OutputInterface $output)
  {
    if (!$input->getOption('init'))
      return;

    $options = array(
      $this->getDefinition()->getOption('website-name'),
      $this->getDefinition()->getOption('web-source-dir'),
      $this->getDefinition()->getOption('web-prod-dir'),
      $this->getDefinition()->getOption('web-user'),
      $this->getDefinition()->getOption('exclude-file'),
      $this->getDefinition()->getOption('include-file'),
      $this->getDefinition()->getOption('backup-sources'),
      $this->getDefinition()->getOption('backup-destination'),
      $this->getDefinition()->getOption('db-name'),
      $this->getDefinition()->getOption('db-user'),
      $this->getDefinition()->getOption('db-pass'),
    );

    $dialog = $this->getDialogHelper();

    foreach ($options as $option) {
      $name = $option->getName();
      $value = $input->getOption($name);

      if (!$value)
        $value = $option->getDefault();

      $value = $dialog->ask($output, $dialog->getQuestion($name, $value), $value);


      if ($option->isArray()) {
        $array = array($value);

        while ('y' == $dialog->ask($output, $dialog->getQuestion('Do you want to add another?', 'Y/n'), 'y')) {
          $temp = $dialog->ask($output, $dialog->getQuestion($name, null), null);
          if (!is_null($temp))
            $array[] = $temp;
        }

        $value = $array;
      }

      $input->setOption($name, $value);
    }


    fputs(fopen('../config2.yml', 'w'), Yaml::dump($input->getOptions()));
  }

}
