<?php

namespace DS\DemoBundle\Tests\Validator;

use DS\DemoBundle\Command\Validator\DirectoryExists;

class DirectoryExistsTest extends \PHPUnit_Framework_TestCase
{

    public function testValidate()
    {
        $validator = new DirectoryExists();

        $fakeFile = __FILE__ . 'fake-file';

        $this->assertFalse($validator->validate($fakeFile));
        $this->assertRegExp('/.*directory.*not.*exist.*/', $validator->getErrorMessage());

        $file = __FILE__;

        $this->assertFalse($validator->validate($file));
        $this->assertRegExp('/.*is.*file.*not.*directory.*/', $validator->getErrorMessage());

        $fakeDirectory = __DIR__ . '/fake-directory';

        $this->assertFalse($validator->validate($fakeDirectory));
        $this->assertRegExp('/.*directory.*not.*exist.*/', $validator->getErrorMessage());

        $realDirectory = __DIR__;

        $this->assertTrue($validator->validate($realDirectory));
        $this->assertEquals('', $validator->getErrorMessage());
        
        $null = null;

        $this->assertTrue($validator->validate($null));
        $this->assertEquals('', $validator->getErrorMessage());
        
        $emtpyString = '';

        $this->assertTrue($validator->validate($emtpyString));
        $this->assertEquals('', $validator->getErrorMessage());
        
        $arrayDirectories = array(
            $fakeDirectory,
            $realDirectory,
        );

        $this->assertFalse($validator->validate($arrayDirectories));
        $this->assertRegExp('/.*directory.*not.*exist.*/', $validator->getErrorMessage());

        $emptyArrayDirectories = array();

        $this->assertTrue($validator->validate($emptyArrayDirectories));
        $this->assertEquals('', $validator->getErrorMessage());
    }

}
