<?php

namespace App\Providers;

use Illuminate\Mail\MailServiceProvider as BaseMailServiceProvider;
use Illuminate\Support\Facades\Config;

class MailServiceProvider extends BaseMailServiceProvider
{
    /**
     * Register the Swift Transport instance.
     *
     * @return void
     */
    protected function registerSwiftTransport()
    {
        parent::registerSwiftTransport();
        
        // Override SMTP transport to disable SSL verification
        $this->app->extend('swift.transport', function ($transport, $app) {
            if (Config::get('mail.default') === 'smtp') {
                $transport->setStreamOptions([
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true,
                    ],
                ]);
            }
            
            return $transport;
        });
    }
}