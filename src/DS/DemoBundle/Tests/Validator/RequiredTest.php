<?php

namespace DS\DemoBundle\Tests\Validator;

use DS\DemoBundle\Command\Validator\Required;

class RequiredTest extends \PHPUnit_Framework_TestCase
{

    public function testValidate()
    {
        $validator = new Required();

        $null = null;

        $this->assertFalse($validator->validate($null));
        $this->assertRegExp('/.*value.*required.*/', $validator->getErrorMessage());

        $emptyString = '';

        $this->assertFalse($validator->validate($emptyString));
        $this->assertRegExp('/.*value.*required.*/', $validator->getErrorMessage());

        $emptyArray = array();

        $this->assertFalse($validator->validate($emptyArray));
        $this->assertRegExp('/.*value.*required.*/', $validator->getErrorMessage());

        $aNumber = 23.0;

        $this->assertTrue($validator->validate($aNumber));
        $this->assertEquals('', $validator->getErrorMessage());

        $aString = '23.0';

        $this->assertTrue($validator->validate($aString));
        $this->assertEquals('', $validator->getErrorMessage());

        $anArray = array(23.0);

        $this->assertTrue($validator->validate($anArray));
        $this->assertEquals('', $validator->getErrorMessage());
    }

}
