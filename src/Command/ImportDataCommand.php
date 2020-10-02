<?php

namespace App\Command;

use App\Service\Import\Importer;
use Doctrine\DBAL\Driver\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportDataCommand
 * @package App\Command
 */
class ImportDataCommand extends Command
{
    private $sources = [
        'rbk' => 'https://www.rbc.ru/',
    ];

    // " php bin/console app:import-articles
    protected static $defaultName = 'app:import-articles';

    /**
     * @var string
     */
    protected static $url;

    protected function configure()
    {
        // ...
    }

    /** @var Importer */
    private $importer;

    public function __construct(Importer $importer)
    {
        $this->importer = $importer;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->sources as $parserName => $source) {
    
            $output->writeln("<info>Starting import from {$source}</info>");
            $countItems = $this->importer->processData($source, $parserName);
            $output->writeln("<info>Parsed {$countItems} articles</info>");
        }

        return Command::SUCCESS;
    }
}