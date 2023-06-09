<?php

namespace App\Services;

class TelegramService
{
    public function send($message)
    {
        $data = array(
            'companycode' => 'coeeac36b530817',
            'data' => array(
                array(
                    'message' => json_encode(['error' =>$message ])
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
