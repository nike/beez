<?php

namespace DS\DemoBundle\Command\Validator;

use DS\DemoBundle\Command\Validator\Validator;

class Required extends Validator
{

  public function validate($value)
  {
    if (empty($value)) {
      $this->errorMessage = 'A value is required';
      return false;
    }

    return true;
  }

}
