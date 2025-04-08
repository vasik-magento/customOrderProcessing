<?php

declare(strict_types=1);

namespace Vendor\CustomOrderProcessing\Api;

use Vendor\CustomOrderProcessing\Api\Data\OrderStatusUpdateResultInterface;

interface OrderStatusManagementInterface
{
    /**
     * @param string $incrementId
     * @param string $status
     * @return OrderStatusUpdateResultInterface
     */
    public function updateStatus($incrementId, $status);
}
