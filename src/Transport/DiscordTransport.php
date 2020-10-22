<?php

namespace Xylis\JCDecaux\Transport;

class DiscordTransport extends BaseTransport
{
    /** @var string */
    protected $webhookUrl = 'https://discordapp.com/api/webhooks/';

    public function send(string $message, bool $debug = false): void
    {
        if ($this->webhookUrl === null) {
            throw new \Exception("Webhook must be defined");
        }

        $this->client->request('POST', $this->webhookUrl, [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'json' => array_merge([
                'content' => $message
            ], $this->options)
        ]);
    }
}
