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
    $this
      ->setName('website:deploy')
      ->setDescription('Deploy a website')
      ->addArgument('configuration', InputArgument::OPTIONAL, 'Configuration file', '.beez/config.yml')
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
//      ->addOption('force', 'x', InputOption::VALUE_NONE, 'Force execution')
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
//    $force = $input->getOption('force');
    // Load configuration
    $configuration = Yaml::parse($input->getArgument('configuration'));

    // Variables from config
    $websiteName = $input->getOption('website-name') ? $input->getOption('website-name') : $configuration['website_name'];
    $excludeList = $input->getOption('exclude-file') ? $input->getOption('exclude-file') : $configuration['exclude_file'];
    $includeList = $input->getOption('include-file') ? $input->getOption('include-file') : $configuration['include_file'];
//    $deployDir = $configuration['deploy_dir'];
    $backupSources = $input->getOption('backup-sources') ? $input->getOption('backup-sources') : $configuration['backup_sources'];
    $backupDestination = $input->getOption('backup-destination') ? $input->getOption('backup-destination') : $configuration['backup_destination'];
    $webSourceDir = $input->getOption('web-source-dir') ? $input->getOption('web-source-dir') : $configuration['web_source_dir'];
    $webProdDir = $input->getOption('web-prod-dir') ? $input->getOption('web-prod-dir') : $configuration['web_prod_dir'];
    $webUser = $input->getOption('web-user') ? $input->getOption('web-user') : $configuration['web_user'];
//    $webGroup = $configuration['web_group'];
    $dbName = $input->getOption('db-name') ? $input->getOption('db-name') : $configuration['db_name'];
    $dbUser = $input->getOption('db-user') ? $input->getOption('db-user') : $configuration['db_user'];
    $dbPass = $input->getOption('db-pass') ? $input->getOption('db-pass') : $configuration['db_pass'];

    $o = array(
      $websiteName,
      $excludeList,
      $includeList,
      $backupSources,
      $backupDestination,
      $webSourceDir,
      $webProdDir,
      $webUser,
      $dbName,
      $dbUser,
      $dbPass,
    );
    
    print_r($o);
//    // Archive site directory
//    $cmd = new nbArchiveCommand();
//    $cmdLine = sprintf('%s/%s.tgz %s --add-timestamp --force', $backupDestination, $websiteName, implode(' ', $backupSources));
//    $this->executeCommand($cmd, $cmdLine, $force, $verbose);
//
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
  }

}
