<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Symfony\Component\Console\Exception\NamespaceNotFoundException;
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
    protected $dontReport = [];

    protected array $dontReportToSentry = [
        AuthenticationException::class,
        CommandNotFoundException::class,
        DoNotReportToSentryException::class,
        MethodNotAllowedHttpException::class,
        NamespaceNotFoundException::class,
        NotFoundHttpException::class,
        ValidationException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = ['password', 'password_confirmation'];

    /**
     * Report or log an exception.
     */
    public function report(Throwable $exception): void
    {
        if ($this->shouldReport($exception)) {
            Log::error($exception->getMessage());
        }

        if ($this->shouldReportToSentry($exception)) {
            if (app()->bound('sentry')) {
                // if sentry send it to sentry
                app('sentry')->captureException($exception);
            }
        }

        parent::report($exception);
    }

    protected function shouldReportToSentry($exception): bool
    {
        return is_null(Arr::first($this->dontReportToSentry, fn ($type) => $exception instanceof $type));
    }
}
