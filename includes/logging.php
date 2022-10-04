<?php
    function get_client_ip() {
        $ip = "";
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
    function logme($message) {
        $timestamp = date(DATE_ISO8601, time());
        $client_ip = get_client_ip();

        $logline_array = array(
            $timestamp,
            $client_ip,
            implode(LOGFILE_SEPARATOR,$message)
        );

        file_put_contents(LOG_DIRECTORY.LOG_NAME.'.log', implode("\t",$logline_array).PHP_EOL, FILE_APPEND);
    }
?>