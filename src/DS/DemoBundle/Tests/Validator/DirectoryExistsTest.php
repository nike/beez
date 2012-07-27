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
    }

}
