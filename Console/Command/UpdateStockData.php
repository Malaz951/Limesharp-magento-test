<?php

declare(strict_types=1);

namespace Limesharp\UpdateStockData\Console\Command;

use Limesharp\UpdateStockData\Model\Service\ConfigService;
use Limesharp\UpdateStockData\Model\Service\ProductStockService;
use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateStockData extends Command
{
    private const FILE_NAME = 'file_name';

    /**
     * @var ProductStockService
     */
    private $productStockService;

    /**
     * @var ConfigService
     */
    private $config;

    /**
     * @param ProductStockService $productStockService
     * @param ConfigService $config
     * @param string|null $name
     */
    public function __construct(
        ProductStockService $productStockService,
        ConfigService       $config,
        string              $name = null
    )
    {
        $this->productStockService = $productStockService;
        parent::__construct($name);
        $this->config = $config;
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('limesharp:update-stock-data');
        $this->setDescription('Update stock data according to specific csv file');
        $this->setDefinition(
            [
                new InputArgument(
                    self::FILE_NAME,
                    InputArgument::REQUIRED,
                    'File name inside var/import'
                )
            ]
        );

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->config->isModuleEnabled()) {
            $output->writeln("The module is disabled from the configuration");
            return Cli::RETURN_SUCCESS;
        }

        $fileName = (string)$input->getArgument(self::FILE_NAME);

        $this->productStockService->updateStockData(
            $fileName
        );

        $output->writeln('Reindexing...');
        shell_exec('php bin/magento indexer:reindex');

        $output->writeln('Flushing cache...');
        shell_exec('php bin/magento cache:flush');

        return Cli::RETURN_SUCCESS;
    }
}
