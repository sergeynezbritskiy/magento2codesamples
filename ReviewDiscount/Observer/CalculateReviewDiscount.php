<?php

namespace Magecom\ReviewDiscount\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class CalculateReviewDiscount
 *
 * @package Magecom\Reviews\Observer
 */
class CalculateReviewDiscount implements ObserverInterface
{

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Magecom\ReviewDiscount\Model\DiscountRule\DiscountCalculator
     */
    private $discountCalculator;

    /**
     * @var \Magecom\ReviewDiscount\Helper\CouponHelper
     */
    private $couponHelper;

    /**
     * CalculateReviewDiscount constructor.
     *
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magecom\ReviewDiscount\Model\DiscountRule\DiscountCalculator $discountCalculator
     * @param \Magecom\ReviewDiscount\Helper\CouponHelper $couponHelper
     */
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magecom\ReviewDiscount\Model\DiscountRule\DiscountCalculator $discountCalculator,
        \Magecom\ReviewDiscount\Helper\CouponHelper $couponHelper

    )
    {
        $this->messageManager = $messageManager;
        $this->discountCalculator = $discountCalculator;
        $this->couponHelper = $couponHelper;
        $this->customerSession = $customerSession;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Review\Model\Review $model */
        $model = $observer->getEvent()->getData('object');
        $entityId = $model->getEntityId();
        if ($entityId === $model->getEntityIdByCode(\Magento\Review\Model\Review::ENTITY_PRODUCT_CODE)) {
            $discount = $this->discountCalculator->calculateForReview($model);
        } else {
            $discount = 0;
        }
        if ($discount > 0) {
            $couponCode = $this->couponHelper->generateCouponCodeForDiscount($discount);
            if ($this->customerSession->isLoggedIn()) {
                $this->couponHelper->appendCouponCode($this->customerSession->getCustomer(), $couponCode);
            }
            $msg = __('Congratulations, you got a discount %1%. Please save this code and use it in checkout to get a discount: %2', $discount, $couponCode);
            $this->messageManager->addSuccessMessage($msg);
        }
    }

}