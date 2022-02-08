<?php

declare(strict_types=1);

namespace Limesharp\UpdateStockData\Cron;

use Limesharp\UpdateStockData\Model\Service\ConfigService;
use Limesharp\UpdateStockData\Model\Service\ProductStockService;

class UpdateStockData
{
    private const STOCK_DATA_FILE_NAME = 'stock.csv';

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
     */
    public function __construct(
        ProductStockService $productStockService,
        ConfigService       $config
    )
    {
        $this->productStockService = $productStockService;
        $this->config = $config;
    }

    /**
     * @return void
     */
    public function execute()
    {
        if (!$this->config->isModuleEnabled()) {
            return;
        }

        $this->productStockService->updateStockData(
            static::STOCK_DATA_FILE_NAME
        );

        shell_exec('php bin/magento indexer:reindex');
        shell_exec('php bin/magento cache:flush');
    }
}
