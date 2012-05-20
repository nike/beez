<?php

namespace DS\DemoBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use DS\DemoBundle\Command\Command;

class FilesystemSyncCommand extends Command
{

  protected function configure()
  {
    $this
      ->setName('filesystem:sync')
      ->setDescription('Synchronize two directories')
      ->addArgument('source', InputArgument::REQUIRED, 'Source directory')
      ->addArgument('target', InputArgument::REQUIRED, 'Directory to synchronize')
      ->addOption('delete', '', InputOption::VALUE_NONE, 'Delete files on destination when synchronize')
      ->addOption('include-file', '', InputOption::VALUE_REQUIRED, 'File that contains a list of include patterns')
      ->addOption('exclude-file', '', InputOption::VALUE_REQUIRED, 'File that contains a list of exclude patterns')
      ->addOption('owner', '', InputOption::VALUE_REQUIRED, 'Owner of the target directory')
      ->addForceOption()
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $includeFile = $input->getOption('include-file');
    $excludeFile = $input->getOption('exclude-file');
    
    if ($includeFile && file_exists($includeFile))
      $includeFile = sprintf('--include-from "%s"', $includeFile);
    
    if ($excludeFile && file_exists($excludeFile))
      $excludeFile = sprintf('--exclude-from "%s"', $excludeFile);

    $delete = $input->getOption('delete') ? '--delete' : '';

    // Trailing slash must be added after sanitize dir
    $source = preg_replace('/\/+$/', '', $input->getArgument('source')) . '/';
    $target = preg_replace('/\/+$/', '', $input->getArgument('target'));

    $commandLine = sprintf('rsync -azoChpAv %s %s %s %s %s %s', $force, $includeFile, $excludeFile, $delete, $source, $target);

    $owner = $input->getOption('owner');
    if ($owner)
      $commandLine = sprintf('sudo -u %s %s', $owner, $commandLine);
    
    $this->addCommandLine($commandLine);
    
    return parent::execute($input, $output);
  }

}
