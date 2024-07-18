<?php

namespace Undjike\LmtNotificationChannel\Tests;

use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;
use Undjike\LmtNotificationChannel\Exceptions\CouldNotSendNotification;
use Undjike\LmtNotificationChannel\LmtChannel;
use Undjike\LmtNotificationChannel\LmtMessage;
use Undjike\LmtNotificationChannel\Requests\SendMessageRequest;
use function PHPUnit\Framework\assertNotEmpty;

class SendMessageTest extends TestCase
{
    /**
     * @return void
     * @throws GuzzleException
     * @throws CouldNotSendNotification
     */
    public function test_send_message_success(): void
    {
        $message = (new LmtMessage())
            ->sender('Automa')
            ->body('New test of SMS push!');

        $phones = ['237697777205'];

        $auth = [
            'X-Api-Key' => '',
            'X-Secret' => '',
        ];

        $response = SendMessageRequest::execute($message, $phones, $auth);

        $opt = LmtChannel::interpretResponse($response->getBody());

        
    }
}
