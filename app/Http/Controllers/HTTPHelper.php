<?php


namespace App\Http\Controllers;


use GuzzleHttp\Client;
use Log;

class HTTPHelper
{

    public static function get($endpoint, $headers = null, $formParams = null) {
        return self::processHttp('GET', $endpoint, $headers, $formParams);
    }

    public static function post($endpoint, $headers = null, $formParams = null) {
        return self::processHttp('POST', $endpoint, $headers, $formParams);
    }

    public static function put($endpoint, $headers = null, $formParams = null) {
        return self::processHttp('PUT', $endpoint, $headers, $formParams);
    }

    private static function processHttp($method, $endpoint, $headers, $formParams) {
        //Execute HTTP call
        try {
            $options = [];
            if ($headers) {
                $options['headers'] = $headers;
            }
            if ($formParams) {
                $options['form_params'] = $formParams;
            }
            Log::info("Calling {$method} {$endpoint}...");
            $response = (new Client())->request($method, $endpoint, $options);
        } catch (\Exception $e) {
            Log::info("Unable to complete HTTP Request to `{$endpoint}`! `{$e->getMessage()}`");
            Log::info($e->getTraceAsString());
            return ['error' => $e->getMessage()];
        }
        //Process Response Data
        $code = $response->getStatusCode();
        if ($code < 200 || $code > 299) {
            $reason = $response->getReasonPhrase();
            $message = "Unable to complete HTTP Request to `{$endpoint}`! `{$reason}`, HTTP: `{$code}`";
            Log::info($message);
            return ['error' => $message];
        }
        Log::info("Response received for {$method} {$endpoint}.");
        $contents = $response->getBody()->getContents();
        $bodyContents = json_decode($contents, true);
        return ['contents' => $bodyContents];
    }

}
