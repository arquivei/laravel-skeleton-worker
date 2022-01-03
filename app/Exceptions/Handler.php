<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Http\Middleware\HeadersMiddleware;
use Core\Dependencies\ContextualLogger;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Arquivei\LogAdapter\Log;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{

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
        'password',
        'password_confirmation',
    ];

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $exception
     * @return Response
     * @throws Throwable
     */
    public function render($request, Throwable $exception)
    {
        if (App::environment('local')) {
            return parent::render($request, $exception);
        }

        if ($exception instanceof NotFoundHttpException) {
            return $this->getResponse(Response::HTTP_NOT_FOUND);
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            return $this->getResponse(Response::HTTP_METHOD_NOT_ALLOWED);
        }

        if ($exception instanceof ValidationException) {
            return $this->getResponse(Response::HTTP_UNPROCESSABLE_ENTITY);
        }


        /** @var Log $logger */

        // $logger = new LogAdapter();
        $logger = app(Log::class);
        $logger->setTraceId($request->headers->get(HeadersMiddleware::X_TRACEID));
        $logger->error(
            Response::$statusTexts[HttpResponse::HTTP_INTERNAL_SERVER_ERROR],
            ['exception' => $e]
        );


        return $this->getResponse(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    private function getResponse(int $httpResponseCode): Response
    {
        return response([
            'status' => [
                'message' => sprintf(
                    '%s:%s',
                    env('APP_IDENTIFIER'),
                    Response::$statusTexts[$httpResponseCode]
                ),
                'code' => $httpResponseCode
            ],
        ], $httpResponseCode);
    }
}
