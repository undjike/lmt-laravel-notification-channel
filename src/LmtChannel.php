<?php

namespace Undjike\LmtNotificationChannel;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Notifications\Notification;
use Psr\Http\Message\StreamInterface;
use Undjike\LmtNotificationChannel\Exceptions\CouldNotSendNotification;
use Undjike\LmtNotificationChannel\Requests\SendMessageRequest;

class LmtChannel
{
    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param Notification $notification
     * @return array
     * @throws CouldNotSendNotification
     * @throws GuzzleException
     */
    public function send($notifiable, Notification $notification): array
    {
        if (! $recipient = $notifiable->routeNotificationFor('Lmt')) {
            throw CouldNotSendNotification::lmtRespondedWithAnError('Your notifiable instance does not have function routeNotificationForLmt.');
        }

        if (! method_exists($notification, 'toLmt')) {
            throw CouldNotSendNotification::lmtRespondedWithAnError('Your need to define the toLmt method on your notification for it to be sent.');
        }

        $message = $notification->toLmt($notifiable);

        if (is_string($message)) {
            $content = trim($message);
            $message = LmtMessage::create()->body($content);
        }

        if (! $message instanceof LmtMessage) {
            throw CouldNotSendNotification::lmtRespondedWithAnError('Required string or LmtMessage instance as the return type of toLmt.');
        }

        if (empty(trim($message->getBody()))) {
            throw CouldNotSendNotification::lmtRespondedWithAnError('Can\'t send a message with an empty body.');
        }

        if (is_string($recipient)) {
            $recipient = [$recipient];
        }

        if (! is_array($recipient)) {
            throw CouldNotSendNotification::lmtRespondedWithAnError('Expected string or array as recipient.');
        }

        $response = SendMessageRequest::execute($message, $recipient);

        return self::interpretResponse($response->getBody());
    }

    /**
     * @throws Exception
     * @throws CouldNotSendNotification
     */
    public static function interpretResponse(StreamInterface $getBody): array
    {
        $response = json_decode(
            $getBody->getContents(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $resultsSet = self::getResultsSet($response);

        if (! isset($resultsSet) && isset($response['code'])) {
            return self::respond([$response]);
        }

        if (! isset($resultsSet)) {
            throw CouldNotSendNotification::lmtRespondedWithAnError('Unable to parse the response.');
        }

        return self::respond($resultsSet);
    }

    /**
     * @param $response
     * @return array|mixed
     */
    public static function getResultsSet($response)
    {
        if ($results = data_get($response, 'data.results')) {
            return $results;
        }

        if ($results = data_get($response, 'data')) {
            return $results;
        }

        return data_get($response, 'results');
    }

    /**
     * @param array $results
     * @return array
     * @throws CouldNotSendNotification
     */
    public static function respond(array $results): array
    {
        foreach ($results as $result) {
            $errorCode = (int) $result['code'];

            if ($errorCode === 0) {
                continue;
            }

            switch ($errorCode) {
                case -1:
                case 1001:
                    throw CouldNotSendNotification::lmtRespondedWithAnError('Not authenticated.');
                case -2:
                case -3:
                    throw CouldNotSendNotification::lmtRespondedWithAnError('Invalid phone number or operator.');
                case -4:
                    throw CouldNotSendNotification::lmtRespondedWithAnError('Unsupported destination country.');
                case -8:
                    throw CouldNotSendNotification::lmtRespondedWithAnError('Data coding scheme invalid.');
                case -9:
                    throw CouldNotSendNotification::lmtRespondedWithAnError('Service ID not found.');
                case -10:
                    throw CouldNotSendNotification::lmtRespondedWithAnError('Message too long.');
                case -11:
                case 1008:
                    throw CouldNotSendNotification::lmtRespondedWithAnError('Not enough balance.');
                default:
                    throw CouldNotSendNotification::lmtRespondedWithAnError("Error #$errorCode occurred." . json_encode($results));
            }
        }

        return $results;
    }
}
