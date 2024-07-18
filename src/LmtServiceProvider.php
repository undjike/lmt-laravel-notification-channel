<?php

namespace Undjike\LmtNotificationChannel;

use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;

class LmtServiceProvider extends ServiceProvider
{
    public const BASE_URL = 'https://sms.lmtgroup.com/api/v1';

    /**
     * Register the application services.
     *
     * @noinspection ReturnTypeCanBeDeclaredInspection
     * @noinspection PhpUnusedParameterInspection
     */
    public function register()
    {
        Notification::resolved(function (ChannelManager $service) {
            $service->extend('lmt', function ($app) {
                return new LmtChannel();
            });
        });
    }
}
