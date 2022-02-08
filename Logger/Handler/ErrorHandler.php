<?php

declare(strict_types=1);

namespace Limesharp\UpdateStockData\Logger\Handler;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;

class ErrorHandler extends Base
{
    /** @var int */
    protected $loggerType = Logger::ERROR;

    /** @var string */
    protected $fileName = '/var/log/limesharp/error.log';
}
