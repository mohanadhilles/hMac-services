<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Services\HmacService;

use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    public const AccessKey = 'SmVM4fQq';
    public const PrivateKey = '45c48d9f5235c50e5364afcd85c73ae9';

    public function __construct(HmacService $hmacService)
    {
        $this->hmacService = $hmacService;
    }


    public function index()
    {
        $response = $this->hmacService->get('https://sandbox.mintroute.com/voucher/v2/api/voucher');
        return $response->getBody();
    }

    public function home()
    {

        $date = \Carbon\Carbon::now();
        $formattedDate = $date->format('Ymd\THis\Z');
        $sing_to = json_encode(["username" => "testvendor",
            "data" => ["ean" => "PSUK10", "location" => "UAE", "terminal_id" => "T1259",
                "order_id" => "TRX92817312", "request_type" => "purchase", "response_type" => "short"]]);
        $message = 'message to be signed';
        $signature = base64_encode(hash_hmac('sha256', $sing_to, self::PrivateKey, true));

        $client = new Client(
            [
                // Base URI is used with relative requests
                'base_uri' => env('API_URL', 'https://sandbox.mintroute.com/'),
                // You can set any number of default request options.
                'timeout' => 20.0,
            ]
        );

        try {
            $response = $client->request(
                'POST',
                "vendor/api/voucher",
                [
                    'form_params' => [
                        'secret' => "8og48uRK5UpBd74/vOD651hXFDt3TGnDsBdIZ1k6Iws=",
                        'user_id' =>"M1AKAH142"
                    ],
                    'headers' => [
                        'Authorization' => 'algorithm="hmac- sha256",credential="M1AKAH142/20200106",signature="8og48uRK5UpBd74/vOD651hXFDt3TGnDsBdIZ1k6Iws="',
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                        'X-Mint-Date' => 20200106,
                    ]
                ]
            );
            dd($response);
            $response = json_decode($response->getBody()->getContents());
            dd($response->data);
        } catch (\Exception $e) {
          Log::error(''.$e);
        }
    }
}
