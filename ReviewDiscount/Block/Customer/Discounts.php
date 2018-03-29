<?php

namespace Magecom\ReviewDiscount\Block\Customer;

use Magento\Framework\Exception\LocalizedException;

/**
 * Class Discounts
 *
 * @package Magecom\ReviewDiscount\Block\Customer
 */
class Discounts extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magecom\ReviewDiscount\Model\ResourceModel\CustomerCoupon\CollectionFactory
     */
    private $couponCollectionFactory;

    /**
     * @var \Magecom\ReviewDiscount\Model\ResourceModel\CustomerCoupon\Collection
     */
    private $couponCollection;
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * Discounts constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magecom\ReviewDiscount\Model\ResourceModel\CustomerCoupon\CollectionFactory $couponCollectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magecom\ReviewDiscount\Model\ResourceModel\CustomerCoupon\CollectionFactory $couponCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->couponCollectionFactory = $couponCollectionFactory;
        $this->customerSession = $customerSession;
    }

    /**
     * @return \Magecom\ReviewDiscount\Model\ResourceModel\CustomerCoupon\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomerCoupons()
    {
        if ($this->couponCollection === null) {
            $this->couponCollection = $this->couponCollectionFactory->create();
            $this->couponCollection->addFieldToFilter('customer_entity_id', $this->customerSession->getCustomer()->getEntityId());
        }
        return $this->couponCollection;
    }

    /**
     * @param \Magecom\ReviewDiscount\Model\CustomerCoupon $coupon
     * @return string
     */
    public function getCouponHtml(\Magecom\ReviewDiscount\Model\CustomerCoupon $coupon)
    {
        /** @var \Magecom\ReviewDiscount\Block\Customer\Discounts\Item $block */
        $block = $this->getChildBlock('coupon');
        $block->setCustomerCoupon($coupon);
        return $block->toHtml();
    }
}