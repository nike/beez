<?php

namespace DS\DemoBundle\Tests\Command;

use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use DS\DemoBundle\Command\DatabaseInitCommand;
use DS\DemoBundle\Command\ShellQueue;

class DatabaseInitCommandTest extends \PHPUnit_Framework_TestCase
{

    public function testExecute()
    {
        $application = new Application();
        $application->add(new DatabaseInitCommand());

        $shellQueue = $this->getMock('ShellQueue', array('addCommandLine', 'run', 'printQueue'));
        $shellQueue
          ->expects($this->once())
          ->method('addCommandLine')
          ->with($this->matchesRegularExpression('/mysqladmin -uroot create beez/s'))
        ;
        $shellQueue->expects($this->once())
          ->method('run')
        ;

        $command = $application->find('database:init');
        $command->setQueue($shellQueue);
        $commandTester = new CommandTester($command);
        $commandTester->execute(
          array('command' => $command->getName(), 'db-name' => 'beez')
        );
    }

}
