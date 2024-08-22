<?php

namespace Undjike\LmtNotificationChannel;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Notification;
use JsonException;
use Psr\Http\Message\ResponseInterface;
use Undjike\LmtNotificationChannel\Exceptions\CouldNotSendNotification;
use Undjike\LmtNotificationChannel\Requests\SendMessageRequest;

class LmtChannel
{
    /**
     * @param ResponseInterface $response
     *
     * @return array
     * @throws JsonException|CouldNotSendNotification
     */
    public static function responsePayload(ResponseInterface $response): array
    {
        $payload = $response->getBody()->getContents();

        return match ($response->getStatusCode()) {
            400 => throw CouldNotSendNotification::lmtRespondedWithAnError("Invalid request payload.\n$payload"),
            401 => throw CouldNotSendNotification::lmtRespondedWithAnError("Authentication error.\n$payload"),
            200, 201 => json_decode($payload, true, 512, JSON_THROW_ON_ERROR)
        };
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param Notification $notification
     *
     * @return array
     * @throws CouldNotSendNotification
     * @throws GuzzleException|JsonException
     */
    public function send(mixed $notifiable, Notification $notification): array
    {
        $recipient = match (true) {
            is_string($notifiable) => $notifiable,
            $notifiable instanceof AnonymousNotifiable && $notifiable->routeNotificationFor(__CLASS__) => $notifiable->routeNotificationFor(__CLASS__),
            default => $notifiable->routeNotificationFor('lmt')
        };

        if (! $recipient) {
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

        return self::responsePayload($response);
    }
}
