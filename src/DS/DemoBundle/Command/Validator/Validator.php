<?php

namespace DS\DemoBundle\Command\Validator;

abstract class Validator
{

  private $message;
  
  public function __construct($message = '')
  {
    $this->message = $message;
  }
  
  public abstract function validate($value);
  
  public function getMessage()
  {
    return $this->message;
  }

}
