<?php

namespace Xylis\JCDecaux\Transport;

use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class BaseTransport implements TransportInterface
{
    /** @var string */
    protected $webhookUrl = '';

    /** @var HttpClientInterface */
    protected $client;

    /** @var array */
    protected $options;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->client = $httpClient;
    }

    public function configure(array $options = []): TransportInterface
    {
        $this->options = $options;

        return $this;
    }

    public function setWebhookUrl(string $webhookUrl): TransportInterface
    {
        $this->webhookUrl .= $webhookUrl;

        return $this;
    }

    abstract public function send(string $message, bool $debug = false): void;
}
