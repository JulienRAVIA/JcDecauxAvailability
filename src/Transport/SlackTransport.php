<?php

namespace Xylis\JCDecaux\Transport;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class SlackTransport extends BaseTransport
{
    /** @var string */
    protected $webhookUrl = 'https://hooks.slack.com/services/';

    public function send(string $message, bool $debug = true): void
    {
        if ($this->webhookUrl === null) {
            throw new \Exception("Webhook must be defined");
        }

        $this->client->request('POST', $this->webhookUrl, [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'json' => array_merge([
                'text' => $message
            ], $this->options)
        ]);
    }
}
