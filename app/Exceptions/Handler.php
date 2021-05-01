<?php

namespace App\Exceptions;

use App\Mail\ExceptionEmail;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\ErrorHandler\ErrorRenderer\HtmlErrorRenderer;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
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
    protected $dontFlash = ['password', 'password_confirmation'];

    /**
     * Report or log an exception.
     *
     * @param \Throwable $exception
     *
     * @return void
     */
    public function report(Throwable $exception)
    {
        if ($this->shouldReport($exception)) {
            /**
             * send email alert to me
             */
            Log::error($exception->getMessage());
            //$this->sendExceptionEmail($exception);

            if (app()->bound('sentry')) {
                /**
                 * if sentry send it to sentry
                 */
                app('sentry')->captureException($exception);
            }
        }
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable               $exception
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        return parent::render($request, $exception);
    }

    /**
     * Sends an email to the developer about the exception.
     *
     * @return void
     */
    public function sendExceptionEmail(Throwable $exceptionReceived)
    {
        try {
            $exception = FlattenException::create($exceptionReceived);
            $handler = new HtmlErrorRenderer(true); // boolean, true raises debug flag...
            $css = $handler->getStylesheet();
            $content = $handler->getBody($exception);
            Log::debug('Queueing mail for : ' . config('mail.email_to_warn'));
            Mail::to(config('mail.email_to_warn'))
                ->queue(new ExceptionEmail(compact('css', 'content')));
        } catch (Throwable $exception) {
            Log::error($exception);
        }
    }
}
