<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use GuzzleHttp\Client;


class HmacService
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function get($url, $options = [])
    {
        try {
            $apiKey = Config::get('services.hmac.api_key');
            $options['headers']['X-Api-Key'] = $apiKey;
            $options['Authorization'] = $this->generateSignature();

            return $this->client->get($url, $options);

        } catch (\Exception $exception) {
            app('log')->error('get' . $exception->getMessage());
        }
    }

    public function generateSignature()
    {
        try {
            $date = \Carbon\Carbon::now();
            $timestamp = $date->format('Ymd\THis\Z');
            $apiKey = Config::get('services.hmac.api_key');
            $apiSecret = Config::get('services.hmac.api_secret');
            $signature = base64_encode(hash_hmac('sha256', $timestamp . $apiKey, $apiSecret));
            return $signature;

        } catch (\Exception $exception) {
            app('log')->error('generateSignature' . $exception->getMessage());
        }
    }

}
