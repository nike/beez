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
          ->expects($this->at(0))
          ->method('addCommandLine')
          ->will($this->returnValue('mysqladmin -u root create beez'))
        ;
        $shellQueue
          ->expects($this->at(1))
          ->method('addCommandLine')
          ->will($this->returnValue('mysql -u root -e "grant all privileges on beez.* to \'beezuser\'@\'localhost\' identified by \'b33zpwd\'"'))
        ;
        $shellQueue->expects($this->once())
          ->method('run')
        ;

        $command = $application->find('database:init');
        $command->setQueue($shellQueue);
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
          'command' => $command->getName(),
          'db-name' => 'beez',
          'db-user' => 'beezuser',
          'db-pass' => 'b33zpwd',
        ));
    }
    
    public function testExecuteWithMysqlCredentials()
    {
        $application = new Application();
        $application->add(new DatabaseInitCommand());

        $shellQueue = $this->getMock('ShellQueue', array('addCommandLine', 'run', 'printQueue'));
        $shellQueue
          ->expects($this->at(0))
          ->method('addCommandLine')
          ->will($this->returnValue('mysqladmin -u mysqlroot -p mysqlpa55 create beez'))
        ;
        $shellQueue
          ->expects($this->at(1))
          ->method('addCommandLine')
          ->will($this->returnValue('mysql -u mysqlroot -p mysqlpa55 -e "grant all privileges on beez.* to \'beezuser\'@\'localhost\' identified by \'b33zpwd\'"'))
        ;
        $shellQueue->expects($this->once())
          ->method('run')
        ;

        $command = $application->find('database:init');
        $command->setQueue($shellQueue);
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
          'command' => $command->getName(),
          'db-name' => 'beez',
          'db-user' => 'beezuser',
          'db-pass' => 'b33zpwd',
          '--mysql-user' => 'mysqlroot',
          '--mysql-pass' => 'mysqlpa55',
        ));
    }
    
    public function testInteraction()
    {
        $application = new Application();
        $application->add(new DatabaseInitCommand());

        $shellQueue = $this->getMock('ShellQueue', array('addCommandLine', 'run', 'printQueue'));
        $shellQueue->expects($this->never())
          ->method('run')
        ;
        $shellQueue->expects($this->once())
          ->method('printQueue')
        ;
        
        $command = $application->find('database:init');
        $command->setQueue($shellQueue);
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
          'command' => $command->getName(),
          'db-name' => 'beez',
          'db-user' => 'beezuser',
          'db-pass' => 'b33zpwd',
        ));
    }
    
    public function testNoInteraction()
    {
        $application = new Application();
        $application->add(new DatabaseInitCommand());

        $shellQueue = $this->getMock('ShellQueue', array('addCommandLine', 'run', 'printQueue'));
        $shellQueue->expects($this->once())
          ->method('run')
        ;
        $shellQueue->expects($this->never())
          ->method('printQueue')
        ;
        
        $command = $application->find('database:init');
        $command->setQueue($shellQueue);
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
          'command' => $command->getName(),
          'db-name' => 'beez',
          'db-user' => 'beezuser',
          'db-pass' => 'b33zpwd',
          '--no-interaction' => true,
        ));
    }
    
}
