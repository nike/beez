<?php

namespace DS\DemoBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use DS\DemoBundle\Command\Command;

class FilesystemBackupCommand extends Command
{

  protected function configure()
  {
    $this
      ->setName('filesystem:backup')
      ->setDescription('Backup directories')
      ->addArgument('destination', InputArgument::REQUIRED, 'Directory where to put the archive')
      ->addArgument('sources', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Directories to backup')
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $sources = $input->getArgument('sources');
    foreach ($sources as $key => $source) {
      $sources[$key] = preg_replace('/\/+$/', '', $source);
    }
    
    $destination = preg_replace('/\/+$/', '', $input->getArgument('destination'));
    
    if (count($sources) == 1) {
      $archiveName = sprintf('%s-%s.tar.gz', basename($sources[0]), date('YmdHi', time()));
    } else {
      $archiveName = sprintf('backup-%s.tar.gz', date('YmdHi', time()));
    }
    
    if (!is_dir($destination)) {
      @mkdir($destination, 0777, true);
    }

    // Options:
    // c: compress
    // v: verbose
    // z: gzip archive
    // f: archive to file
    // C: root dir in the archived file
    $commandLine = sprintf('tar -czf %s/%s %s', $destination, $archiveName, implode(' ', $sources));

    $this->addCommandLine($commandLine);

    return parent::execute($input, $output);
  }

}
