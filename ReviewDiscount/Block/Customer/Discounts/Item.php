<?php

namespace Magecom\ReviewDiscount\Block\Customer\Discounts;

/**
 * Class Item
 *
 * @package Magecom\ReviewDiscount\Block\Customer\Discounts
 */
class Item extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magecom\ReviewDiscount\Model\CustomerCoupon
     */
    private $customerCoupon;

    /**
     * @return \Magecom\ReviewDiscount\Model\CustomerCoupon
     */
    public function getCustomerCoupon()
    {
        return $this->customerCoupon;
    }

    /**
     * @param \Magecom\ReviewDiscount\Model\CustomerCoupon $customerCoupon
     */
    public function setCustomerCoupon($customerCoupon)
    {
        $this->customerCoupon = $customerCoupon;
    }

}