<?php

namespace DS\DemoBundle\Command\Validator;

use DS\DemoBundle\Command\Validator\Validator;

class DirectoryExists extends Validator
{

    public function validate($value)
    {
        $values = (array) $value;

        if (count($values) == 0)
            return true;

        foreach ($values as $v) {
            if (is_file($v)) {
                $this->errorMessage = sprintf('The value "%s" is a file not a directory', $v);
                return false;
            }

            if (!file_exists($v)) {
                $this->errorMessage = sprintf('The directory "%s" does not exist', $v);
                return false;
            }
        }

        $this->errorMessage = '';
        
        return true;
    }

}
