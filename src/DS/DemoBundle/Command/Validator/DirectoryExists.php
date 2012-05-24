<?php

namespace DS\DemoBundle\Command\Validator;

use DS\DemoBundle\Command\Validator\Validator;

class DirectoryExists extends Validator
{

  public function validate($value)
  {
    if (is_file($value)) {
      $this->errorMessage = 'The value is a file not a directory';
      return false;
    }

    if (!file_exists($value)) {
      $this->errorMessage = 'The directory does not exist';
      return false;
    }

    return true;
  }

}
