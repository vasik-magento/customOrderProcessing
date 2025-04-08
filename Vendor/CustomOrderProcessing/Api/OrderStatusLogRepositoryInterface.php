<?php

declare(strict_types=1);

namespace Vendor\CustomOrderProcessing\Api;

use Vendor\CustomOrderProcessing\Api\Data\OrderStatusLogInterface;
use Magento\Framework\Exception\LocalizedException;

interface OrderStatusLogRepositoryInterface
{
    /**
     * Save log entry
     *
     * @param OrderStatusLogInterface $log
     * @return OrderStatusLogInterface
     * @throws LocalizedException
     */
    public function save(OrderStatusLogInterface $log);
}
