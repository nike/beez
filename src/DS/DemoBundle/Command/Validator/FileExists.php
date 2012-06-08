<?php

namespace DS\DemoBundle\Command\Validator;

use DS\DemoBundle\Command\Validator\Validator;

class FileExists extends Validator
{

  public function validate($value)
  {
    $values = (array) $value;

    if (count($values) == 0)
      return true;

    foreach ($values as $v) {
      if (is_dir($v)) {
        $this->errorMessage = sprintf('The value "%s" is a directory not a file', $v);
        return false;
      }

      if (!file_exists($v)) {
        $this->errorMessage = sprintf('The file "%s" does not exist', $v);
        return false;
      }
    }

    return true;
  }

}
