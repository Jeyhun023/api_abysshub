<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\RelationNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

class Handler extends ExceptionHandler
{
    use ApiResponser;
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
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

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ModelNotFoundException && $request->wantsJson()) {
            return $this->errorResponse(["not_found" => [trans('messages.not_found')] ], JsonResponse::HTTP_NOT_FOUND);
        }
    
        if ($exception instanceof RelationNotFoundException && $request->wantsJson()) {
            return $this->errorResponse(["not_found" => [trans('messages.relation_not_found')] ], JsonResponse::HTTP_NOT_FOUND);
        }

        if ($exception instanceof ThrottleRequestsException && $request->wantsJson()) {
            return $this->errorResponse(["attempt" => [trans('messages.too_many_attempt')] ]);
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function unauthenticated($request, \Illuminate\Auth\AuthenticationException $exception)
    {
        return $exception->redirectTo()
                    ? redirect()->guest($exception->redirectTo())
                    : $this->errorResponse(["token" => trans('auth.unauthorized')]);
    }
}
