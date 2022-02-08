<?php

declare(strict_types=1);

namespace Limesharp\UpdateStockData\Model\Service;

use Exception;
use Limesharp\UpdateStockData\Logger\Logger;
use Magento\InventoryApi\Api\SourceItemsSaveInterface;
use Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory;
use Magento\CatalogInventory\Model\Stock;

class ProductStockService
{
    /**
     * @var SourceItemInterfaceFactory
     */
    private $sourceItemFactory;

    /**
     * @var SourceItemsSaveInterface
     */
    private $sourceItemsSave;

    /**
     * @var CsvFileService
     */
    private $csvFileService;
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param SourceItemInterfaceFactory $sourceItemFactory
     * @param SourceItemsSaveInterface $sourceItemsSave
     * @param CsvFileService $csvFileService
     * @param Logger $logger
     */
    public function __construct(
        SourceItemInterfaceFactory $sourceItemFactory,
        SourceItemsSaveInterface   $sourceItemsSave,
        CsvFileService             $csvFileService,
        Logger                     $logger
    )
    {
        $this->sourceItemFactory = $sourceItemFactory;
        $this->sourceItemsSave = $sourceItemsSave;
        $this->csvFileService = $csvFileService;
        $this->logger = $logger;
    }

    /**
     * Read stock data from the file and update
     * stock data accordingly per inventory source
     *
     * @param string $fileName
     * @param string $source
     * @return void
     */
    public function updateStockData(string $fileName, string $source = 'default'): void
    {
        $stockData = $this->csvFileService->getNewStockData($fileName);
        if (empty($stockData)) {
            return;
        }

        $sourceItems = [];

        foreach ($stockData as $row) {
            $stockStatus = $row['qty'] > 0 ? Stock::STOCK_IN_STOCK : Stock::STOCK_OUT_OF_STOCK;

            $sourceItem = $this->sourceItemFactory->create();

            $sourceItem->setSku($row['sku']);
            $sourceItem->setQuantity($row['qty']);
            $sourceItem->setSourceCode($source);
            $sourceItem->setStatus($stockStatus);

            $sourceItems[] = $sourceItem;
        }

        try {
            $this->sourceItemsSave->execute($sourceItems);
        } catch (Exception $e) {
            $this->logger->info(
                sprintf(
                    'Error while saving stock items data, message: %s, trace: %s',
                    $e->getMessage(),
                    $e->getTraceAsString()
                )
            );
        }
    }
}
