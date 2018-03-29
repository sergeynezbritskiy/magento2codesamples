<?php

namespace Magecom\BorrowHome\Controller\Adminhtml\Order;

/**
 * Class Returned
 * @package Magecom\BorrowHome\Controller\Adminhtml\Order
 */
class Returned extends \Magecom\BorrowHome\Controller\Adminhtml\AbstractOrder
{

    /**
     * This status should be set
     * @return string
     */
    protected function getNewOrderStatusKey()
    {
        return self::ORDER_STATUS_BORROW_HOME_RETURNED;
    }

    /**
     * This state should be set
     * @return string
     */
    protected function getNewOrderStateKey()
    {
        return \Magento\Sales\Model\Order::STATE_CANCELED;
    }

    /**
     * This status should be changed from
     * @return string
     */
    protected function getOldOrderStatusKey()
    {
        return self::ORDER_STATUS_ON_LOAN;
    }
}