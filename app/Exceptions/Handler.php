<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;
use ValidateException;
use TwoTypeException;
use Illuminate\Http\Exception\HttpResponseException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
  /**
   * A list of the exception types that should not be reported.
   *
   * @var array
   */
  protected $dontReport = [
    AuthorizationException::class,
    HttpException::class,
    ModelNotFoundException::class,
    ValidationException::class,
  ];

  /**
   * Report or log an exception.
   *
   * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
   *
   * @param  \Exception  $e
   * @return void
   */
  public function report(Exception $e)
  {
    if (app()->bound('sentry') && $this->shouldReport($e)) {
      app('sentry')->captureException($e);
    }

    parent::report($e);
  }

  /**
   * Render an exception into an HTTP response.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Exception  $e
   * @return \Illuminate\Http\Response
   */
  public function render($request, Exception $e)
  {
    // return parent::render($request, $e);

    $e = $this->renderException($e);
    $status = null === $e->getPrevious()->getCode()
      ? $e->getStatusCode()
      : $e->getPrevious()->getCode();
    $message = (null === json_decode($e->getPrevious()->getMessage(), true)
      ? $e->getPrevious()->getMessage()
      : json_decode($e->getPrevious()->getMessage(), true));

    if (env('APP_DEBUG')) {
      return response()->json([
        'status' => 'error',
        'type' => 'exception',
        'message' => $message,
        'file' => $e->getPrevious()->getFile(),
        'line' => $e->getPrevious()->getLine(),
        'code' => $e->getPrevious()->getCode(),
        'url' => $request->fullUrl(),
        'trace' => $e->getTrace(),
      ], $status);
    } else {
      return response()->json([
        'status' => 'error',
        'type' => 'exception',
        'message' => $message
      ], $status);
    }
  }

  public function renderException($exception)
  {
    $status = Response::HTTP_INTERNAL_SERVER_ERROR;
    if ($exception instanceof HttpResponseException) {
      $status = Response::HTTP_INTERNAL_SERVER_ERROR;
    } elseif ($exception instanceof MethodNotAllowedHttpException) {
      $status = Response::HTTP_METHOD_NOT_ALLOWED;
      $exception = new MethodNotAllowedHttpException([], $exception->getMessage(), $exception);
    } elseif ($exception instanceof NotFoundHttpException) {
      $status = Response::HTTP_NOT_FOUND;
      $exception = new NotFoundHttpException($exception);
    } elseif ($exception instanceof AuthorizationException) {
      $status = Response::HTTP_FORBIDDEN;
      $exception = new AuthorizationException($status);
    } elseif ($exception instanceof ValidateException) {
      $status = Response::HTTP_BAD_REQUEST;
      $exception = new ValidateException($exception->getMessage(), null, $status);
    } elseif ($exception instanceof TwoTypeException) {
      $status = Response::HTTP_BAD_REQUEST;
      $exception = new TwoTypeException($exception->getMessage(), null, $status);
    } elseif ($exception) {
      $exception = new HttpException($status, $exception->getMessage(), $exception);
    }

    return $exception;
  }
}
