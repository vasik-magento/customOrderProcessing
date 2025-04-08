<?php

declare(strict_types=1);

namespace Vendor\CustomOrderProcessing\Model;

use Vendor\CustomOrderProcessing\Api\OrderStatusLogRepositoryInterface;
use Vendor\CustomOrderProcessing\Api\Data\OrderStatusLogInterface;
use Vendor\CustomOrderProcessing\Model\ResourceModel\OrderStatusLog as ResourceModel;
use Magento\Framework\Exception\LocalizedException;

class OrderStatusLogRepository implements OrderStatusLogRepositoryInterface
{
    /**
     * @var ResourceModel
     */
    protected $resource;

    /**
     * @param ResourceModel $resource
     */
    public function __construct(
        ResourceModel $resource
    ) {
        $this->resource = $resource;
    }

    /**
     * @param OrderStatusLogInterface $log
     * @return OrderStatusLogInterface
     * @throws LocalizedException
     */
    public function save(OrderStatusLogInterface $log)
    {
        try {
            $this->resource->save($log);
        } catch (\Exception $e) {
            throw new LocalizedException(__('Could not save log: %1', $e->getMessage()));
        }

        return $log;
    }
}
