<?php

namespace Undjike\LmtNotificationChannel;

class LmtMessage
{
    /**
     * Body of the message
     *
     * @var string
     */
    protected string $body;

    /**
     * Brand name of the message
     *
     * @var string
     */
    protected string $brand = 'LMT';

    /**
     * Encoding of the message
     * GSM7 for standard characters or UCS2 if using special characters and emojis
     *
     * @var string
     */
    protected string $encoding = 'GSM7';

    /**
     * Whether the phone number should be displayed
     *
     * @var bool
     */
    protected bool $displayNumbers = true;

    /**
     * LmtMessage constructor.
     *
     * @param string $body
     */
    public function __construct(string $body = '')
    {
        $this->body($body);
    }

    /**
     * LMT SMS message pseudo-constructor.
     *
     * @param string $body
     * @return LmtMessage
     */
    public static function create(string $body = ''): LmtMessage
    {
        return new static($body);
    }

    /**
     * Set message body
     *
     * @param string $body
     * @return LmtMessage
     */
    public function body(string $body): LmtMessage
    {
        $this->body = trim($body);
        return $this;
    }

    /**
     * Get message body
     *
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * Set message sender
     *
     * @param string $brand
     * @return LmtMessage
     */
    public function sender(string $brand): LmtMessage
    {
        $this->brand = trim($brand);
        return $this;
    }

    /**
     * Get message sender
     *
     * @return string
     */
    public function getSender(): string
    {
        return $this->brand;
    }

    /**
     * Set message encoding
     *
     * @param string $encoding
     * @return LmtMessage
     */
    public function encoding(string $encoding): LmtMessage
    {
        $this->encoding = $encoding;
        return $this;
    }

    /**
     * Get messaging encoding
     *
     * @return string
     */
    public function getEncoding(): string
    {
        return $this->encoding;
    }

    /**
     * Define whether the phone number should be displayed
     *
     * @param bool $show
     * @return LmtMessage
     */
    public function showNumbers(bool $show = true): LmtMessage
    {
        $this->displayNumbers = $show;
        return $this;
    }

    /**
     * Whether the phone number should be hidden
     *
     * @return bool
     */
    public function maskedNumbers(): bool
    {
        return ! $this->displayNumbers;
    }
}
