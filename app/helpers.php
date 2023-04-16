<?php
function snakeToCamelCase($string) {
    $string = str_replace('_', ' ', $string);
    $string = ucwords($string);
    $string = str_replace(' ', '', $string);
    $string = lcfirst($string);
    return $string;
}

function send_to_tg_bot($message){

    $url = 'https://t.kuleshov.studio/api/getmessages';

    //companycode - Индивидуальный код организации (получить у администратора)
    $data = ["companycode" => "co4c31488d6c470", "data" => [["message" => $message]]];

    $data_string = json_encode($data);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);

    curl_close($ch);

    return true;
}
