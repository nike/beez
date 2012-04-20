<?php

namespace DS\DemoBundle\Tests\Command;

use DS\DemoBundle\Command\ShellQueue;
use Symfony\Component\Console\Output\Output;

class ShellQueueTest extends \PHPUnit_Framework_TestCase
{

    public function testAddAndPrint()
    {
        $queue = new ShellQueue();

        $commandLine = 'chmod -R 777 /var/source';
        $queue->addCommandLine($commandLine);
        $commandLine = 'chown -R apache:apache /var/www';
        $queue->addCommandLine($commandLine);

        $expectedOutput = <<<TXT
chmod -R 777 /var/source
chown -R apache:apache /var/www

TXT;
        $output = new TestOutput();
        $queue->printQueue($output);

        $this->assertEquals($expectedOutput, $output->output);
    }

    public function testAddAndRun()
    {
        $queue = new ShellQueue();

        $commandLine = "php -r \"echo 'first';\"";
        $queue->addCommandLine($commandLine);
        $commandLine = "php -r \"echo 'second';\"";
        $queue->addCommandLine($commandLine);

        $output = new TestOutput();
        $queue->run($output);

        $this->assertEquals(1, preg_match("/OK\nOK/s", $output->output));
    }

    public function testRunWithErrors()
    {
        $queue = new ShellQueue();

        $commandLine = "php -r \"echo 'first';\"";
        $queue->addCommandLine($commandLine);
        $commandLine = "this-command-do-not-exists";
        $queue->addCommandLine($commandLine);

        $output = new TestOutput();
        $queue->run($output);

        $this->assertEquals(1, preg_match("/OK\nGeneral error:.+/s", $output->output));
        
        $queue = new ShellQueue();

        $commandLine = "this-command-do-not-exists";
        $queue->addCommandLine($commandLine);
        $commandLine = "php -r \"echo 'first';\"";
        $queue->addCommandLine($commandLine);

        $output = new TestOutput();
        $queue->run($output);

        $this->assertEquals(1, preg_match("/OK\nGeneral error:.+/s", $output->output));

    }
    
    public function testRunStopAtError()
    {
        $queue = new ShellQueue();

        $commandLine = "this-command-do-not-exists";
        $queue->addCommandLine($commandLine);
        $commandLine = "php -r \"echo 'first';\"";
        $queue->addCommandLine($commandLine);

        $output = new TestOutput();
        $queue->run($output);

        $this->assertEquals(0, preg_match("/.+OK.+/", $output->output));

    }

}

class TestOutput extends Output
{

    public $output = '';

    public function clear()
    {
        $this->output = '';
    }

    public function doWrite($message, $newline)
    {
        $this->output .= $message . ($newline ? "\n" : '');
    }

}
