<?php

namespace DS\DemoBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use DS\DemoBundle\Command\Command;

class FilesystemBackupCommand extends Command
{

  protected function configure()
  {
    $this
      ->setName('filesystem:backup')
      ->setDescription('Creates a tar archive of a directory')
      ->addArgument('source', InputArgument::REQUIRED, 'Direcotry to backup')
      ->addArgument('destination', InputArgument::REQUIRED, 'Directory where to put the archive')
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $source = preg_replace('/\/+$/', '', $input->getArgument('source'));
    $destination = preg_replace('/\/+$/', '', $input->getArgument('destination'));
    
    $archiveName = sprintf('%s-%s.tar.gz', basename($source), date('YmdHi', time()));
    $archivePath = dirname($source);
    
    if (!is_dir($destination)) {
      @mkdir($destination, 0777, true);
    }
    
    // Options:
    // c: compress
    // v: verbose
    // z: gzip archive
    // f: archive to file
    // C: root dir in the archived file
    $commandLine = sprintf('tar -czf %s/%s %s -C"%s"', $destination, $archiveName, $source, $archivePath);
    
    $this->queue->addCommandLine($commandLine);
    
    return $this->queue->run($output);
  }

}
