<?php

declare(strict_types=1);

namespace Vendor\CustomOrderProcessing\Api\Data;

interface OrderStatusLogInterface
{
    const LOG_ID     = 'log_id';
    const ORDER_ID   = 'order_id';
    const OLD_STATUS = 'old_status';
    const NEW_STATUS = 'new_status';
    const CREATED_AT = 'created_at';

    public function getLogId();
    public function setLogId($id);

    public function getOrderId();
    public function setOrderId($orderId);

    public function getOldStatus();
    public function setOldStatus($oldStatus);

    public function getNewStatus();
    public function setNewStatus($newStatus);

    public function getCreatedAt();
    public function setCreatedAt($createdAt);
}
