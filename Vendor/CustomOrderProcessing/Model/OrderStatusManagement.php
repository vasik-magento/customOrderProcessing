<?php

declare(strict_types=1);

namespace Vendor\CustomOrderProcessing\Model;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Shipment\Sender\EmailSender;
use Magento\Sales\Model\Order\ShipmentFactory;
use Magento\Sales\Model\Service\InvoiceService;
use Vendor\CustomOrderProcessing\Api\Data\OrderStatusUpdateResultInterface;
use Vendor\CustomOrderProcessing\Api\OrderStatusManagementInterface;
use Psr\Log\LoggerInterface;

class OrderStatusManagement implements OrderStatusManagementInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var InvoiceService
     */
    private $invoiceService;
    /**
     * @var ShipmentFactory
     */
    private $shipmentFactory;
    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentRepository;
    /**
     * @var EmailSender
     */
    private $shipmentEmailSender;
    /**
     * @var OrderStatusUpdateResultInterface
     */
    private $orderStatusUpdateResult;

    /**
     * @param InvoiceService $invoiceService
     * @param ShipmentFactory $shipmentFactory
     * @param OrderRepositoryInterface $orderRepository
     * @param ShipmentRepositoryInterface $shipmentRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param EmailSender $shipmentEmailSender
     * @param OrderStatusUpdateResultInterface $orderStatusUpdateResult
     * @param LoggerInterface $logger
     */
    public function __construct(
        InvoiceService $invoiceService,
        ShipmentFactory $shipmentFactory,
        OrderRepositoryInterface $orderRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        EmailSender $shipmentEmailSender,
        OrderStatusUpdateResultInterface $orderStatusUpdateResult,
        LoggerInterface $logger
    ) {
        $this->invoiceService = $invoiceService;
        $this->shipmentFactory = $shipmentFactory;
        $this->orderRepository = $orderRepository;
        $this->shipmentRepository = $shipmentRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->shipmentEmailSender = $shipmentEmailSender;
        $this->orderStatusUpdateResult = $orderStatusUpdateResult;
        $this->logger = $logger;
    }

    /**
     * @param string $incrementId
     * @param string $status
     * @return OrderStatusUpdateResultInterface
     * @throws LocalizedException
     */
    public function updateStatus($incrementId, $status)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('increment_id', $incrementId)
            ->create();

        $orders = $this->orderRepository->getList($searchCriteria)->getItems();

        // Check if the order with $incrementId exists
        if (empty($orders)) {
            throw new LocalizedException(__('Order not found.'));
        }

        $order = array_shift($orders);

        $currentStatus = $order->getStatus();

        // Allowed status <from> => <to>
        $allowedTransitions = [
            'pending' => ['processing'],
            'processing' => ['complete'],
        ];

        if (
            !isset($allowedTransitions[$currentStatus]) ||
            !in_array($status, $allowedTransitions[$currentStatus])
        ) {
            throw new LocalizedException(__(
                'Cannot change status from "%1" to "%2".',
                $currentStatus,
                $status
            ));
        }

        // Define the state based on the status
        $state = match ($status) {
            'processing' => Order::STATE_PROCESSING,
            'complete' => Order::STATE_COMPLETE,
            default => Order::STATE_PENDING_PAYMENT,
        };

        // Set both the status and state
        $order->setStatus($status);
        $order->setState($state);

        // Process order for invoice and shipment w.r.t status
        $this->processOrder($order, $status);

        $this->orderRepository->save($order);

        $this->orderStatusUpdateResult->setSuccess(true);
        $this->orderStatusUpdateResult->setMessage('Order status updated successfully.');

        return $this->orderStatusUpdateResult;
    }

    /**
     * @param Order $order
     * @param string $newStatus
     */
    public function processOrder(Order $order, string $newStatus)
    {
        try {
            // Create invoice if moving to processing
            if ($newStatus === Order::STATE_PROCESSING && $order->canInvoice()) {
                $invoice = $this->invoiceService->prepareInvoice($order);
                $invoice->register();
                $invoice->pay();
                $invoice->save();

                $this->logger->info('Invoice created for Order #' . $order->getIncrementId());
            }

            // Create shipment if moving to complete
            if (($newStatus === Order::STATE_COMPLETE || $newStatus === 'complete') && $order->canShip()) {

                $shipmentItems = [];

                foreach ($order->getAllItems() as $orderItem) {
                    if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                        continue;
                    }

                    $shipmentItems[$orderItem->getId()] = $orderItem->getQtyToShip();
                }

                if (empty($shipmentItems)) {
                    throw new LocalizedException(
                        __('No shippable items found for order #%1.', $order->getIncrementId())
                    );
                }

                $shipment = $this->shipmentFactory->create($order, $shipmentItems);
                $shipment->register();

                $order->setIsInProcess(true);

                $this->shipmentRepository->save($shipment);

                // Trigger email notification for shipment
                $this->shipmentEmailSender->send($order, $shipment);

                $this->logger->info('Shipment created for Order #' . $order->getIncrementId());
            }
        } catch (\Exception $e) {
            $this->logger->error('Order processing failed: ' . $e->getMessage());
        }
    }
}
