<?php

namespace Undjike\LmtNotificationChannel\Tests;

use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use PHPUnit\Framework\TestCase;
use Undjike\LmtNotificationChannel\Exceptions\CouldNotSendNotification;
use Undjike\LmtNotificationChannel\LmtChannel;
use Undjike\LmtNotificationChannel\LmtMessage;
use Undjike\LmtNotificationChannel\Requests\SendMessageRequest;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertGreaterThanOrEqual;

class SendMessageTest extends TestCase
{
    /**
     * @return void
     * @throws GuzzleException
     * @throws CouldNotSendNotification|JsonException
     */
    public function test_send_message_success(): void
    {
        $message = (new LmtMessage())
            ->sender('CBC')
            ->body($body = 'New test of SMS push!');

        $phones = ['237697777205'];

        $auth = [
            'X-Api-Key' => '<API_KEY>',
            'X-Secret' => '<API_SECRET>'
        ];

        $response = SendMessageRequest::execute($message, $phones, $auth);

        $payload = LmtChannel::responsePayload($response);

        assertEquals($body, $payload['content']);
        assertGreaterThanOrEqual($payload['totalRecipients'], 1);
    }
}
