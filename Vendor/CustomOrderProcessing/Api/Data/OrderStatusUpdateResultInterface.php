<?php

declare(strict_types=1);

namespace Vendor\CustomOrderProcessing\Api\Data;

interface OrderStatusUpdateResultInterface
{
    /**
     * Get success status.
     *
     * @return bool
     */
    public function getSuccess();

    /**
     * Get message.
     *
     * @return string
     */
    public function getMessage();

    /**
     * Set success status.
     *
     * @param bool $success
     * @return void
     */
    public function setSuccess($success);

    /**
     * Set message.
     *
     * @param string $message
     * @return void
     */
    public function setMessage($message);
}
