<?php

declare(strict_types=1);

namespace Vendor\CustomOrderProcessing\Observer;

use Exception;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Sales\Model\Order;
use Vendor\CustomOrderProcessing\Api\OrderStatusLogRepositoryInterface;
use Vendor\CustomOrderProcessing\Model\OrderStatusLogFactory;
use Psr\Log\LoggerInterface;

class OrderStatusObserver implements ObserverInterface
{
    /**
     * @var OrderStatusLogFactory
     */
    protected $logFactory;
    /**
     * @var OrderStatusLogRepositoryInterface
     */
    protected $logRepository;
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param OrderStatusLogFactory $logFactory
     * @param OrderStatusLogRepositoryInterface $logRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        OrderStatusLogFactory $logFactory,
        OrderStatusLogRepositoryInterface $logRepository,
        LoggerInterface $logger
    ) {
        $this->logFactory = $logFactory;
        $this->logRepository = $logRepository;
        $this->logger = $logger;
    }

    /**
     * @param Observer $observer
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getEvent()->getOrder();

        $oldStatus = $order->getOrigData('status');
        $newStatus = $order->getStatus();

        if ($oldStatus != $newStatus) {
            // Log the change
            $log = $this->logFactory->create();
            $log->setOrderId($order->getId());
            $log->setOldStatus($oldStatus);
            $log->setNewStatus($newStatus);
            $log->setCreatedAt((new \DateTime())->format('Y-m-d H:i:s'));
            $this->logRepository->save($log);
        }
    }
}
