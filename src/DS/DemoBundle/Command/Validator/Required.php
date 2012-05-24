<?php

namespace DS\DemoBundle\Command\Validator;
use DS\DemoBundle\Command\Validator\Validator;

class Required extends Validator
{

  public function validate($value)
  {
    if (empty($value))
      return false;
    
    return true;
  }

}
