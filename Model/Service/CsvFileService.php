<?php

declare(strict_types=1);

namespace Limesharp\UpdateStockData\Model\Service;

use Exception;
use Limesharp\UpdateStockData\Logger\Logger;
use Magento\Framework\File\Csv;
use Magento\Framework\Filesystem\DirectoryList;

class CsvFileService
{
    /**
     * @var Csv
     */
    private $csv;

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param Csv $csv
     * @param DirectoryList $directoryList
     * @param Logger $logger
     */
    public function __construct(
        Csv           $csv,
        DirectoryList $directoryList,
        Logger        $logger
    )
    {
        $this->csv = $csv;
        $this->directoryList = $directoryList;
        $this->logger = $logger;
    }

    /**
     * Read the csv file and return needed data as an array
     *
     * @param string $fileName
     * @return array
     */
    public function getNewStockData(string $fileName): array
    {
        try {
            $filePath = sprintf(
                '%s/import/%s',
                $this->directoryList->getPath('var'),
                $fileName
            );

            $csvData = $this->csv->setDelimiter('|')->getData($filePath);
        } catch (Exception $e) {
            $this->logger->error(
                sprintf(
                    'message: %s, trace: %s',
                    $e->getMessage(),
                    $e->getTraceAsString()
                ));

            return [];
        }

        if (empty($csvData)) {
            $this->logger->debug(
                sprintf('%s file is empty', $filePath)
            );

            return [];
        }

        $newStockData = [];
        foreach ($csvData as $data) {
            if (isset($data[0]) && isset($data[1])) {
                $newStockData[] = [
                    'sku' => (string)$data[0],
                    'qty' => (float)$data[1]
                ];
            }
        }

        return $newStockData;
    }
}
