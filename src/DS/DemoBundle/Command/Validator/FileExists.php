<?php

namespace DS\DemoBundle\Command\Validator;

use DS\DemoBundle\Command\Validator\Validator;

class FileExists extends Validator
{

  public function validate($value)
  {
    if (is_dir($value)) {
      $this->errorMessage = 'The value is a directory not a file';
      return false;
    }

    if (!file_exists($value)) {
      $this->errorMessage = 'The file does not exist';
      return false;
    }

    return true;
  }

}
