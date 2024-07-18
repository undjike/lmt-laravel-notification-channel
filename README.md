<p align="center"><img src="https://lmtgroup.com/wp-content/uploads/2019/09/logo-300.png" alt="logo"></p>

<p align="center">
<a href="https://packagist.org/packages/undjike/lmt-laravel-notification-channel"><img src="https://poser.pugx.org/undjike/lmt-laravel-notification-channel/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/undjike/lmt-laravel-notification-channel"><img src="https://poser.pugx.org/undjike/lmt-laravel-notification-channel/license.svg" alt="License"></a>
<a href="https://packagist.org/packages/undjike/lmt-laravel-notification-channel"><img src="https://poser.pugx.org/undjike/lmt-laravel-notification-channel/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/undjike/lmt-laravel-notification-channel"><img src="https://poser.pugx.org/undjike/lmt-laravel-notification-channel/dependents.svg" alt="Dependents"></a>
</p>

## Introduction

This is a package for Laravel Applications which enables you to send notifications through LMT SMS Channel.

The package uses <a href="https://lmtgroup.com/offre-sms">LMT API Service</a> to perform SMS dispatching.

## Installation

This package can be installed via composer. Just type :

```bash
composer require undjike/lmt-laravel-notification-channel
```

## Usage

After installation, configure your services in `congig/services.php` by adding :

```php
<?php

return [
    //...

    'lmt' => [
        'key' => env('LMT_API_KEY'), // Your credentials are expected here
        'secret' => env('LMT_API_SECRET')
    ],
];
```

Once this is done, you can create your notification as usual.

```php
<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Undjike\LmtNotificationChannel\LmtChannel;
use Undjike\LmtNotificationChannel\LmtMessage;

class LmtNotification extends Notification
{
    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [LmtChannel::class]; // or return 'lmt';
    }

    /**
     * @param $notifiable
     * @return mixed
     */
    public function toLmt($notifiable)
    {
        return LmtMessage::create()
            ->body('Type here you message content...')
            ->sender('Brand name');
        // or return 'Type here you message content...';
    }
}

```

To get this stuff completely working, you need to add this
to your notifiable model.


```php
    /**
     * Attribute to use when addressing LMT SMS notification
     *
     * @returns string|array
     */
    public function routeNotificationForLmt()
    {
        return $this->phone_number; // Can be a string or an array of valid phone numbers
    }
```

Enjoy !!!

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
