<?php

namespace Magecom\ReviewDiscount\Model\ResourceModel\CustomerCoupon;

/**
 * Class Collection
 *
 * @package Magecom\ReviewDiscount\Model\ResourceModel\Rule
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Initialization here
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Magecom\ReviewDiscount\Model\CustomerCoupon::class,
            \Magecom\ReviewDiscount\Model\ResourceModel\CustomerCoupon::class
        );
    }

}