<?php

namespace Undjike\LmtNotificationChannel\Requests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Undjike\LmtNotificationChannel\LmtMessage;
use Undjike\LmtNotificationChannel\LmtServiceProvider;

class SendMessageRequest
{
    /**
     * @throws GuzzleException
     * @noinspection PhpUndefinedFunctionInspection
     */
    public static function execute(LmtMessage $message, array $addressees, array $auth = null): ResponseInterface
    {
        $client = new Client(['base_uri' => LmtServiceProvider::BASE_URL]);

        $auth ??= [
            'X-Api-Key' => config('services.lmt.key'),
            'X-Secret' => config('services.lmt.secret')
        ];

        $data = [
            'msisdn' => $addressees,
            'senderId' => $message->getSender(),
            'message' => $message->getBody(),
            'flag' => $message->getEncoding(),
            'maskedMsisdn' => $message->maskedNumbers()
        ];

        return $client->post('/api/v1/pushes', [
            'json' => $data,
            'headers' => $auth + ['Content-Type' => 'application/json'],
            'http_errors' => false
        ]);
    }
}
