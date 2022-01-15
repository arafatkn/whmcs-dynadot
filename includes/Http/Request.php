<?php

namespace Arafatkn\WhmcsDynadot\Http;

class Request
{
    /**
     * Get Response from Http.
     *
     * @throws \Exception
     */
    public static function get($url, $options = [])
    {
        if (!function_exists('curl_version')) {
            if (function_exists('file_get_contents')) {
                return file_get_contents($url);
            }

            throw new \Exception("Please install CURL or enable file_get_contents()");
        }

        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_HEADER, 'Content-Type:application/xml' );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        $result = curl_exec( $ch );

        if ($result === false) {
            throw new \Exception(curl_error($ch));
        }

        curl_close( $ch );

        return $result;
    }
}