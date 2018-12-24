<?php

namespace App\Exceptions;

use \Exception;
use Illuminate\Contracts\Validation\Validator;

class ValidateException extends Exception
{
  private $pubMessage;

  private $devMessage;

  private $statusCode = 422;

  public function __construct(
    $pubMessage = null,
    Validator $devMassage = null,
    $statusCode = 422,
    Exception $previous = null
  ) {
    $this->devMassage = $devMassage;
    $this->pubMessage = $pubMessage;
    $this->statusCode = $statusCode;
    $message = [
      'pubMessage' => $pubMessage,
      'devMessage' => $devMassage->errors()
    ];

    parent::__construct(json_encode($message), $statusCode, $previous);
  }

  public function getPubMessage()
  {
    return $this->pubMessage;
  }

  public function getDevMessage()
  {
    return $this->devMessage;
  }

  public function getStatusCode()
  {
    return $this->statusCode;
  }

  public function getResponse()
  {
    return json_decode($this->getMessage(), true);
  }

  public function errors()
  {
    return $this->validator->errors();
  }

  public function getErrorMessages()
  {
    return $this->validator->errors()->messages();
  }
}