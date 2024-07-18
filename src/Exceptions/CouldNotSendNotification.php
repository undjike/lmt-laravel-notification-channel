<?php

namespace Undjike\LmtNotificationChannel\Exceptions;

use Exception;

class CouldNotSendNotification extends Exception
{
    public static function lmtRespondedWithAnError($response): CouldNotSendNotification
    {
        return new static("LMT SMS service responded with an error: $response");
    }
}
