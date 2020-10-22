# JC Decaux Availaibiltiy

This is a tool for notifying you the availables stands and bikes for 
JCDecaux self-service bicycles.

This tool use the [JC Decaux Developer API](https://developer.jcdecaux.com#/)

You must ask for a developer key, check [this page](https://developer.jcdecaux.com#/opendata/vls?page=getstarted)
(you simply have to [create an account](https://developer.jcdecaux.com/#/signup) and request a key from your account, it takes 2minutes)

It sends you discord (in a channel) or slack messages like this : 

![Example message](https://i.imgur.com/wXGBU8V.png)

## Get started

- duplicate .env.dist file as .env.
- launch `composer install`

## How to use

The app.php is ready, with transports and configuration.
If you do not want to use a transport (Discord or Slack), remove the line from
the app.php

```php
$slackTransport = new Xylis\JCDecaux\Transport\SlackTransport($httpClient);
$slackTransport->setWebhookUrl($_ENV['SLACK_WEBHOOK']);
$slackTransport->configure([]);
```
```php
->addTransport($slackTransport, 'slack');
```
and make sure the default transport in the .env file isn't the one you deleted.

## Configuration

Simply add your JCDecaux API Key in the parameter **JCDECAUX_API** of your .env file

For Discord, add your webtoken id in the DISCORD_WEBHOOK parameter:
https://discordapp.com/api/webhooks/**760091511525736448/JpjH9RjMtMDI69bPjxC5ZuyDxxxXv_3R-SIxyX7k3Hg1TMnyI9pZetxxxwRwD9C7aab**
(token after the url)

For slack add your webtoken id in the DISCORD_WEBHOOK parameter: 
https://hooks.slack.com/services/**T02T2JBT6/B01A2OWPSIX/OjKIlpUA748Vu9n6ksYTDn0H**
(token after the url)

Your .env file would look like
```
JCDECAUX_API=xxx

DISCORD_WEBHOOK=760091511525736448/JpjH9RjMtMDI69bPjxC5ZuyDxxxXv_3R-SIxyX7k3Hg1TMnyI9pZetxxxwRwD9C7aab
SLACK_WEBHOOK=T02T2JBT6/B01A2OWPSIX/OjKIlpUA748Vu9n6ksYTDn0H

DEFAULT_TRANSPORT=discord
```

### How to use

`php app.php jcdecaux:notify-availables --departure=9020 --arrival=9006 --arrival=9044 --contract=lyon`

Parameters : 
- `--departure`. You can provide many (must be numeric)
- `--arrival`. You can provide many (must be numeric)
- `--contract`. Specify the contract to use ([among theses](https://developer.jcdecaux.com#/opendata/vls?page=static))
- `--transport`. Specify the transport to use (he must be configured in the app.php file). If not provided, 
the default transport in `.env` file will be used.

#### Want to receive recurrent notifications ?

Simply configure a CRON task with the command. 😎

Use https://crontab.guru or https://crontab-generator.org

### Want to contribute ?

Do not hesitate to submit Pull requests with adjustements or new features. 🥰