<?php

namespace DS\DemoBundle\Tests\Validator;

use DS\DemoBundle\Command\Validator\FileExists;

class FileExistsTest extends \PHPUnit_Framework_TestCase
{

    public function testValidate()
    {
        $validator = new FileExists();

        $directory = __DIR__;

        $this->assertFalse($validator->validate($directory));
        $this->assertRegExp('/.*is.*directory.*not.*file.*/', $validator->getErrorMessage());

        $fakeFile = __FILE__ . 'fake-file';

        $this->assertFalse($validator->validate($fakeFile));
        $this->assertRegExp('/.*file.*not.*exist.*/', $validator->getErrorMessage());

        $realFile = __FILE__;

        $this->assertTrue($validator->validate($realFile));
        $this->assertEquals('', $validator->getErrorMessage());

        $null = null;

        $this->assertTrue($validator->validate($null));
        $this->assertEquals('', $validator->getErrorMessage());

        $emtpyString = '';

        $this->assertTrue($validator->validate($emtpyString));
        $this->assertEquals('', $validator->getErrorMessage());

        $arrayFiles = array(
            $fakeFile,
            $realFile,
        );

        $this->assertFalse($validator->validate($arrayFiles));
        $this->assertRegExp('/.*file.*not.*exist.*/', $validator->getErrorMessage());

        $emptyArrayFiles = array();

        $this->assertTrue($validator->validate($emptyArrayFiles));
        $this->assertEquals('', $validator->getErrorMessage());
    }

}
