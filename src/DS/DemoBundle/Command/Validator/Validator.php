<?php

namespace DS\DemoBundle\Command\Validator;

abstract class Validator
{

  protected $errorMessage;

  public function __construct()
  {
    $this->errorMessage = 'Not valid';
  }

  public abstract function validate($value);

  public function getErrorMessage()
  {
    return $this->errorMessage;
  }

}
