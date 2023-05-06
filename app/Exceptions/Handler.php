<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Exception|Throwable $e)
    {
        $data = array(
            'companycode' => 'coeeac36b530817',
            'data' => array(
                array(
                    'message' => json_encode(['error' => $e->getMessage()])
                )
            )
        );


        $json_data = json_encode($data);

// Set up cURL to make the HTTP request
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://t.kuleshov.studio/api/getmessages',
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $json_data,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
            CURLOPT_TIMEOUT => 1,
            CURLOPT_CONNECTTIMEOUT => 1,
        ));

// Fire off the HTTP request and immediately close the cURL handle
        curl_exec($curl);
        curl_close($curl);
        if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response()->json(['error' => 'Record_not_found.'], 404);
        }
        return response()->json(['error' => $e->getMessage()], 500);
        return parent::render($request, $e);
    }

}
