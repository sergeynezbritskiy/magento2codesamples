<?php

namespace Magecom\BorrowHome\Controller\Adminhtml\Order;

/**
 * Class Processed
 * @package Magecom\BorrowHome\Controller\Adminhtml\Order
 */
class Processed extends \Magecom\BorrowHome\Controller\Adminhtml\AbstractOrder
{

    /**
     * This status should be set
     * @return string
     */
    protected function getNewOrderStatusKey()
    {
        return self::ORDER_STATUS_ON_LOAN;
    }

    /**
     * This state should be set
     * @return string
     */
    protected function getNewOrderStateKey()
    {
        return \Magento\Sales\Model\Order::STATE_PROCESSING;
    }

    /**
     * This status should be changed from
     * @return string
     */
    protected function getOldOrderStatusKey()
    {
        return self::ORDER_STATUS_BORROW_HOME;
    }
}