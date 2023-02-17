<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client;

class HmacServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        try {
            $this->app->singleton('HmacService', function ($app) {
                $config = $app->make('config')->get('services.hmac');
                $date = \Carbon\Carbon::now();
                $timestamp = $date->format('Ymd\THis\Z');

               return new Client([
                    'base_uri' => $config['base_uri'],
                    'form_params' => [
                        'secret' => $config['api_secret'],
                    ],
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                        'X-Mint-Date' => $timestamp,
                    ],
                ]);
            });

        } catch (\Exception $exception) {
            app('log')->error('register'.$exception->getMessage());
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
