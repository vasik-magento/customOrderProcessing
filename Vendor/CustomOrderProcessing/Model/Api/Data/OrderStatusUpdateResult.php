<?php

declare(strict_types=1);

namespace Vendor\CustomOrderProcessing\Model\Api\Data;

use Vendor\CustomOrderProcessing\Api\Data\OrderStatusUpdateResultInterface;

class OrderStatusUpdateResult implements OrderStatusUpdateResultInterface
{
    /**
     * @var bool
     */
    protected $success;
    /**
     * @var string
     */
    protected $message;

    /**
     * @return bool
     */
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param bool $success
     */
    public function setSuccess($success)
    {
        $this->success = $success;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }
}
