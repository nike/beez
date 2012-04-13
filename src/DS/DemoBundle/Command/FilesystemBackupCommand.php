<?php

namespace Demo\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

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
    $source = realpath($input->getArgument('source'));
    $destination = realpath($input->getArgument('destination'));
    
    $archiveName = sprintf('%s-%s.tar.gz', basename($source), date('YmdHi', time()));
    $archivePath = dirname($source);
    
//    if(!is_dir($destinationDir)) {
//      if(!$createDestinationDir) 
//        throw new InvalidArgumentException("Archive directory not found: " . $destinationDir);
//      
//      $this->getFileSystem()->mkdir($destinationDir, true);
//    }
    if (!is_dir($destination)) {
      $output->writeln('<error>TODO: get a normalized path when destination does not exist</error>');
      die;
      mkdir($destination, 0777, true);
    }
    
    // Options:
    // c: compress
    // v: verbose
    // z: gzip archive
    // f: archive to file
    // C: root dir in the archived file
    $commandLine = sprintf('tar -czf %s/%s %s -C"%s"', $destination, $archiveName, $source, $archivePath);
//    $output->writeln(sprintf('<info>%s</info>', $commandLine));
//    return 0;

    $process = new Process($commandLine);
    
    if ($process->run()) {
      $output->writeln(sprintf('<error>%s:</error>', $process->getExitCodeText()));
      $output->writeln('');
      $output->writeln(sprintf('<error>%s</error>', $process->getErrorOutput()));
      return $process->getExitCode();
    } else {
      $output->writeln(sprintf('<info>%s</info>', $process->getErrorOutput()));
      $output->writeln(sprintf('<info>%s</info>', $process->getExitCodeText()));
    }
    
    // Ok
    return 0;
  }

}
