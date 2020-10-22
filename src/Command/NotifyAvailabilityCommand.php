<?php

namespace Xylis\JCDecaux\Command;

use Xylis\JCDecaux\Transport\BaseTransport;
use Xylis\JCDecaux\Transport\TransportInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class NotifyAvailabilityCommand extends BaseCommand
{
    /** @var string */
    protected static $defaultName = 'jcdecaux:notify-availables';

    /** @var TransportInterface[] */
    protected $transports;

    protected function configure(): void
    {
        $this
            ->addOption(
                'departure',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Departure stations',
                []
            )
            ->addOption(
                'arrival',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Arrival stations',
                []
            )
            ->addOption(
                'transport',
                null,
                InputOption::VALUE_OPTIONAL,
                'Transport to send message with'
            )
            ->addOption(
                'contract',
                null,
                InputOption::VALUE_REQUIRED,
                ''
            )
            ->addOption(
                'debug',
                null,
                InputOption::VALUE_NONE,
                'Debug sending message ?'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $contract */
        $contract = $input->getOption('contract');

        /** @var string $transport */
        $transport = $input->getOption('transport') ?? 'default';

        /** @var array $departures */
        $departures = $input->getOption('departure');

        /** @var array $arrival */
        $arrival = $input->getOption('arrival');

        $velovStations = [];
        $velovStations['departure'] = $departures;
        $velovStations['arrival'] = $arrival;

        $date = new \DateTime('now', new \DateTimeZone('Europe/Paris'));

        $message = "*Today at " . $date->format('H:i') . "* \n";
        foreach ($velovStations as $travel => $stations) {
            $type = $travel === 'departure' ? 'bikes' : 'stands';
            foreach ($stations as $station) {
                $informations = $this->getVelovStationInformation($station, $contract);

                $message .= $this->generateAvailabilityMessage($informations, $type) . "\n\n";
            }
        }

        $debug = (bool) $input->getOption('debug');

        try {
            $this->send($message, $transport, $debug);
            $output->write('Informations has been posted on ' . $transport);
        } catch (\Exception $exception) {
            $output->write($exception->getMessage());
        }

        return self::SUCCESS;
    }

    private function generateAvailabilityMessage(
        array $informationsStation,
        string $type,
        string $status = 'OPEN'
    ): string {
        $connected = $informationsStation['connected'] ? 'connected' : 'disconnected';
        $emoji = $type === 'bikes' ? 'bike' : 'parking';

        return sprintf(
            ":%s: - %s (%d %s available/%d) (%s/%s)",
            $emoji,
            $informationsStation['name'],
            $informationsStation['totalStands']['availabilities'][$type],
            $type,
            $informationsStation['totalStands']['capacity'],
            $status,
            $connected
        );
    }

    private function getVelovStationInformation(int $stationNumber, string $contract = null): array
    {
        return $this->httpClient->request('GET', sprintf("%s/stations/%s", $this->url, $stationNumber), [
            'query' => [
                'contract' => $contract,
                'apiKey' => $this->jcDecauxApiKey
            ]
        ])->toArray();
    }

    public function addTransport(BaseTransport $transport, string $name): self
    {
        $this->transports[strtolower($name)] = $transport;

        return $this;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Exception
     */
    public function initialize(InputInterface $input, OutputInterface $output): void
    {
        /** @var array $departures */
        $departures = $input->getOption('departure');

        /** @var array $arrival */
        $arrival = $input->getOption('arrival');

        if (count($departures) === 0 && count($arrival) === 0) {
            throw new \Exception("You must provide at least departure or arrival stations");
        }

        if (!$this->arrayOfInt(array_merge($departures, $arrival))) {
            throw new \Exception("Stations must be numeric values");
        }

        parent::initialize($input, $output);
    }

    /**
     * @param string $name
     * @return TransportInterface
     * @throws \Exception
     */
    protected function getTransport(string $name): TransportInterface
    {
        if (count($this->transports) === 0) {
            throw new \Exception("There is no transport defined");
        }
        if (!isset($this->transports[strtolower($name)])) {
            throw new \Exception("This transport does not exist");
        }

        return $this->transports[strtolower($name)];
    }

    public function setDefaultTransport(string $name): self
    {
        $this->transports['default'] = $this->getTransport(strtolower($name));

        return $this;
    }

    public function send(string $message, string $transport = 'default', bool $debug = false): void
    {
        $transport = $this->getTransport(strtolower($transport));

        $transport->send($message, $debug);
    }

    private function arrayOfInt(array $array): bool
    {
        foreach ($array as $value) {
            if (!is_numeric($value)) {
                return false;
            }
        }

        return true;
    }
}
