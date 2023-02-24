<?php

namespace App\Exceptions;

use ErrorException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\UnauthorizedException ;
use Spatie\Permission\Exceptions\UnauthorizedException as UnautorizedExceptionSpatie;
use Spatie\Permission\Exceptions\UnauthorizedException as ExceptionsUnauthorizedException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
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

        // Se lanzan errores personalizados
        public function render($request, Throwable $e)
        {
            switch ($e) {
                case $e instanceof ErrorException:
                    return response()->json([
                        'message' => 'Ha ocurrido un error inesperado.',
                        'error' => $e->getMessage(),
                    ], 500);
                    break;

                case $e instanceof ModelNotFoundException:
                    return response()->json([
                        'message' => 'El modelo al que quiere acceder no existe.',
                        'error' => $e->getMessage(),
                    ], 404);
                    break;

                case $e instanceof AuthenticationException:
                    return response()->json([
                        'message' => 'Debe iniciar sesión.',
                        'error' => $e->getMessage(),
                    ], 401);
                    break;

                case $e instanceof MethodNotAllowedHttpException:
                    return response()->json([
                        'message' => 'El método actual no es compatible con esta ruta.',
                        'error' => $e->getMessage(),
                    ], 405);
                    break;

                case $e instanceof NotFoundHttpException:
                    return response()->json([
                        'message' => 'El recurso no se encuentra.',
                        'error' => $e->getMessage(),
                    ], 404);
                    break;

                case $e instanceof QueryException:
                    return response()->json([
                        'message' => 'La conexión con la base de datos se ha interrumpido.',
                        'error' => $e->getMessage(),
                    ], 500);
                    break;

                case $e instanceof UnauthorizedException:
                    return response()->json([
                        'message' => 'No tienes permisos para esta acción.',
                        'error' => $e->getMessage(),
                    ], 403);
                    break;

                case $e instanceof ExceptionsUnauthorizedException:
                    return response()->json([
                        'message' => 'Usuario con los roles incorrectos.',
                        'error' => $e->getMessage(),
                    ], 403);
                    break;

                default:
                    break;
            }

            return parent::render($request, $e);
        }
}
