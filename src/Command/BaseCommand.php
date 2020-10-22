<?php

namespace Xylis\JCDecaux\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class BaseCommand extends Command
{
    /** @var string */
    protected $url = 'https://api.jcdecaux.com/vls/v3';

    /** @var HttpClientInterface */
    protected $httpClient;

    /** @var string */
    protected $jcDecauxApiKey;

    /** @var string */
    protected $contract;

    public const CONTRACTS = [
        'lyon' => "Vélo'v",
        'brisbane' => 'CityCycle',
        'bruxelles' => 'villo',
        'namur' => "Li bia velo",
        'santander' => "Tusbic",
        'amiens' => "Velam",
        'cergy-pontoise' => "Velo2",
        'creteil' => "Cristolib",
        'marseille' => "Le vélo",
        'mulhouse' => "VéloCité",
        "nancy" => "vélOstan'lib",
        "nantes" => "Bicloo",
        "rouen" => "cy'clic",
        "toulouse" => "Vélô"
    ];

    public function __construct(
        HttpClientInterface $httpClient,
        string $jcDecauxApiKey
    ) {
        $this->httpClient = $httpClient;

        parent::__construct();
        $this->jcDecauxApiKey = $jcDecauxApiKey;
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        /** @var string|null $contract */
        $contract = $input->getOption('contract');

        if ($contract === null) {
            throw new \Exception("You must provide contract");
        }

        if (!isset(self::CONTRACTS[$contract])) {
            throw new \Exception("This contract does not exist");
        }
    }
}
