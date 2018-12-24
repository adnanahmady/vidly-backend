<?php

namespace App\Exceptions;

use \Exception;

class TwoTypeException extends Exception
{
  private $pubMessage;

  private $devMessage;

  private $statusCode;

  public function __construct(
    $pubMessage = null,
    $devMessage = null,
    $statusCode = 400,
    Exception $previous = null
  ) {
    $this->pubMessage = $pubMessage;
    $this->devMessage = $devMessage;
    $message = [
      'pubMessage' => $pubMessage,
      'devMessage' => $devMessage
    ];

    parent::__construct(json_encode($message), $statusCode, $previous);
  }

  public function getStatusCode() {
    return $this->statusCode;
  }

  public function getResponse() {
    return json_decode($this->getMessage(), true);
  }

  public function getPubMessage()
  {
    return $this->pubMessage;
  }

  public function getDevMessage()
  {
    return $this->devMessage;
  }
}