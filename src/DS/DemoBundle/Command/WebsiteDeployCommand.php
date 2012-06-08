<?php

namespace DS\DemoBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use DS\DemoBundle\Command\CompositeCommand;
use DS\DemoBundle\Command\Validator\Required;
use DS\DemoBundle\Command\Validator\FileExists;
use DS\DemoBundle\Command\Validator\DirectoryExists;

class WebsiteDeployCommand extends CompositeCommand
{

  protected function configure()
  {
    $this
      ->setName('website:deploy')
      ->setDescription('Deploy a website')
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
//      ->addOption('init', 'i', InputOption::VALUE_NONE, 'First deploy')
      ->addConfigurationArgument()
      ->addForceOption()
    ;

    $this
      ->addOptionValidators('web-source-dir', array(new Required(), new DirectoryExists()))
      ->addOptionValidators('web-prod-dir', array(new Required(), new DirectoryExists()))
      ->addOptionValidators('exclude-file', array(new Required(), new FileExists()))
      ->addOptionValidators('include-file', array(new Required(), new FileExists()))
      ->addOptionValidators('backup-sources', array(new Required(), new DirectoryExists()))
      ->addOptionValidators('backup-destination', array(new Required(), new DirectoryExists()))
    ;
  }

  protected function initialize(InputInterface $input, OutputInterface $output)
  {
    $this->loadConfiguration($input);
    $this->validateInput($input);

    $excludeFile = $input->getOption('exclude-file');
    $includeFile = $input->getOption('include-file');
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
      $this->addCommandArray(array(
        'command' => 'filesystem:backup',
        'sources' => $backupSources,
        'destination' => $backupDestination,
      ), $output);
    }

    // Dump
    if (!empty($dbName) && !empty($dbUser) && !empty($dbPass)) {
      $this->addCommandArray(array(
        'command' => 'database:dump',
        'destination' => $backupDestination,
        'db-name' => $dbName,
        'db-user' => $dbUser,
        'db-pass' => $dbPass,
      ), $output);
    }

    // Sync
    if (!empty($webSourceDir) && !empty($webProdDir)  && !empty($webUser) && !empty($includeFile) && !empty($excludeFile)) {
      $this->addCommandArray(array(
        'command' => 'filesystem:sync',
        'source' => $webSourceDir,
        'target' => $webProdDir,
        '--owner' => $webUser,
        '--include-file' => $includeFile,
        '--exclude-file' => $excludeFile,
        '--delete' => true,
      ), $output);
    }
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
