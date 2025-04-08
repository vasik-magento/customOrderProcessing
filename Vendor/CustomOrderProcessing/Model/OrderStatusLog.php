<?php

declare(strict_types=1);

namespace Vendor\CustomOrderProcessing\Model;

use Magento\Framework\Model\AbstractModel;
use Vendor\CustomOrderProcessing\Api\Data\OrderStatusLogInterface;

class OrderStatusLog extends AbstractModel implements OrderStatusLogInterface
{
    protected function _construct()
    {
        $this->_init(ResourceModel\OrderStatusLog::class);
    }

    public function getLogId()
    {
        return $this->getData(self::LOG_ID);
    }

    public function setLogId($id)
    {
        return $this->setData(self::LOG_ID, $id);
    }

    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    public function getOldStatus()
    {
        return $this->getData(self::OLD_STATUS);
    }

    public function setOldStatus($oldStatus)
    {
        return $this->setData(self::OLD_STATUS, $oldStatus);
    }

    public function getNewStatus()
    {
        return $this->getData(self::NEW_STATUS);
    }

    public function setNewStatus($newStatus)
    {
        return $this->setData(self::NEW_STATUS, $newStatus);
    }

    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}
