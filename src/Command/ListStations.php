<?php

namespace Xylis\JCDecaux\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ListStations extends BaseCommand
{
    /** @var string */
    protected static $defaultName = 'jcdecaux:stations';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string|null $contract */
        $contract = $input->getOption('contract');

        $stations = $this->getVelovStations($contract);
        foreach ($stations as $station) {
            $stats = $station['totalStands'];
            $output->writeln(
                sprintf(
                    "%d | %s (%s) (%d bikes available, %d stands availables, %d total stands)",
                    $station['number'],
                    $station['name'],
                    $station['contractName'],
                    $stats['availabilities']['bikes'],
                    $stats['availabilities']['stands'],
                    $stats['capacity']
                )
            );
        }

        return self::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addOption('contract', null, InputOption::VALUE_REQUIRED);
    }

    private function getVelovStations(string $contract = null): array
    {
        return $this->httpClient->request('GET', $this->url . "/stations", [
            'query' => [
                'contract' => $contract,
                'apiKey' => $this->jcDecauxApiKey
            ]
        ])->toArray();
    }
}
