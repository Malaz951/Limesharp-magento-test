<?php

declare(strict_types=1);

namespace Limesharp\UpdateStockData\Model\Service;

use Magento\Framework\App\Config\ScopeConfigInterface;

class ConfigService
{
    private const MODULE_STATUS_PATH_XML = 'stock_data_updater/general/module_status';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return bool
     */
    public function isModuleEnabled(): bool
    {
        return (bool) $this->scopeConfig->getValue(
            static::MODULE_STATUS_PATH_XML
        );
    }
}
