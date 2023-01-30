<?php

namespace App\Exceptions;

use App\Traits\ApiResponder;
use Exception;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponder;
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ClientException) {
            $errorJson = json_decode((string) $exception->getResponse()->getBody()->getContents());

            $code = $exception->getCode();
            switch ($code) {
                case Response::HTTP_BAD_REQUEST:
                    $message = $errorJson->message;
                    break;
                case Response::HTTP_UNPROCESSABLE_ENTITY:
                    $message = $errorJson->errors ?? $errorJson->message;
                    break;

                default:
                    $message = $errorJson->message;
                    break;
            }
            return $this->failureResponse($message, $code);
        }

        if ($exception instanceof HttpException) {
            $code = $exception->getStatusCode();
            $message = Response::$statusTexts[$code];

            return $this->failureResponse($message, $code);
        }

        if ($exception instanceof AuthorizationException) {
            return $this->failureResponse("You are not authorized to access this resource", Response::HTTP_FORBIDDEN);
        }

        if ($exception instanceof ModelNotFoundException) {
            $model = strtolower(class_basename($exception->getModel()));

            return $this->failureResponse("Coud not find any {$model} with the given id", Response::HTTP_NOT_FOUND);
        }

        if ($exception instanceof AuthenticationException) {

            return $this->failureResponse("You are not logged in", Response::HTTP_UNAUTHORIZED);
        }

        if ($exception instanceof ValidationException) {
            $errors = $exception->validator->errors()->getMessages();

            return $this->failureResponse($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (config('app.env') == 'production') {
            if ($exception instanceof Exception) {
                return $this->failureResponse("An error occurred!",  Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        return parent::render($request, $exception);
    }
}
