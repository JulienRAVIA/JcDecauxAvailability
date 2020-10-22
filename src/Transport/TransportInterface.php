<?php

namespace Xylis\JCDecaux\Transport;

interface TransportInterface
{
    public function send(string $message, bool $debug = false): void;

    public function configure(array $options = []): TransportInterface;

    public function setWebhookUrl(string $webhookUrl): TransportInterface;
}
