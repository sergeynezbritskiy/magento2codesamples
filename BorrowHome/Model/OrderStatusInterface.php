<?php

namespace Magecom\BorrowHome\Model;

/**
 * Interface OrderStatusInterface
 * @package Magecom\BorrowHome\Model
 */
interface OrderStatusInterface
{
    const ORDER_STATUS_BORROW_HOME = 'borrow_home';
    const ORDER_STATUS_ON_LOAN = 'on_loan';
    const ORDER_STATUS_BORROW_HOME_RETURNED = 'borrow_home_returned';
}