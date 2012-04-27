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

        $this->assertRegExp("/OK\nOK/s", $output->output);
    }

    public function testAddNullOrEmpty()
    {
        $queue = new ShellQueue();

        $commandLine = null;
        $queue->addCommandLine($commandLine);

        $output = new TestOutput();
        $queue->printQueue($output);

        $this->assertEquals('', $output->output);

        $commandLine = '';
        $queue->addCommandLine($commandLine);

        $output = new TestOutput();
        $queue->printQueue($output);

        $this->assertEquals('', $output->output);
    }

    public function testRunWithErrors()
    {
        $queue = new ShellQueue();

        $commandLine = "php -r \"echo 'first';\"";
        $queue->addCommandLine($commandLine);
        $commandLine = "this-command-does-not-exist";
        $queue->addCommandLine($commandLine);

        $output = new TestOutput();
        $queue->run($output);

        $this->assertTrue(preg_match("/OK\nGeneral error:.+/s", $output->output) || preg_match("/OK\nCommand not found:.+/s", $output->output));
    }

    public function testRunAndStopAtError()
    {
        $queue = new ShellQueue();

        $commandLine = "this-command-does-not-exist";
        $queue->addCommandLine($commandLine);
        $commandLine = "php -r \"echo 'second';\"";
        $queue->addCommandLine($commandLine);

        $output = new TestOutput();
        $queue->run($output);

        $this->assertTrue(preg_match("/General error:.+/s", $output->output) || preg_match("/Command not found:.+/s", $output->output));
        $this->assertEquals(0, preg_match("/.+OK.+/", $output->output));
    }
    
    public function testStripMultipleSpacesInCommandLine()
    {
        $queue = new ShellQueue();
        $commandLine = '   this  command    has    got multiple   spaces  ';
        $actualResult = $queue->addCommandLine($commandLine);

        $this->assertEquals('this command has got multiple spaces', $actualResult);
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
