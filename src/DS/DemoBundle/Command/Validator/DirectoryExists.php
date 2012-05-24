<?php

namespace DS\DemoBundle\Command\Validator;
use DS\DemoBundle\Command\Validator\Validator;

class DirectoryExists extends Validator
{

  public function validate($value)
  {
//    if (!dir_exists($value))
//      return false;
    
    return true;
  }

}
