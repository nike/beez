<?php

namespace DS\DemoBundle\Command\Validator;
use DS\DemoBundle\Command\Validator\Validator;

class FileExists extends Validator
{

  public function validate($value)
  {
    if (!file_exists($value))
      return false;
    
    return true;
  }

}
