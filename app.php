<?php

require 'vendor/autoload.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpClient;

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

$httpClient = new HttpClient\NativeHttpClient();

$slackTransport = new Xylis\JCDecaux\Transport\SlackTransport($httpClient);
$slackTransport->setWebhookUrl($_ENV['SLACK_WEBHOOK']);
$slackTransport->configure([]);

$discordTransport = new Xylis\JCDecaux\Transport\DiscordTransport($httpClient);
$discordTransport->setWebhookUrl($_ENV['DISCORD_WEBHOOK']);
$discordTransport->configure([
    'username' => 'Combienkicon les vélov ?'
]);

$application = new Application();
$application->add(
    (new Xylis\JCDecaux\Command\NotifyAvailabilityCommand(
        $httpClient, $_ENV['JCDECAUX_API'])
    )->addTransport($slackTransport, 'slack')
    ->addTransport($discordTransport, 'discord')
    ->setDefaultTransport($_ENV['DEFAULT_TRANSPORT'])
);
$application->add(new Xylis\JCDecaux\Command\ListStations($httpClient, $_ENV['JCDECAUX_API']));

$application->run();