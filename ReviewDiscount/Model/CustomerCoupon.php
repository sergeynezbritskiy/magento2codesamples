<?php

namespace Magecom\ReviewDiscount\Model;

/**
 * Class CustomerCoupon
 *
 * @package Magecom\ReviewDiscount\Model
 * @method integer getCouponCode()
 * @method void setCouponCode(string $couponCode)
 * @method string getCustomerEntityId()
 * @method void setCustomerEntityId(int $customerId)
 * @method string getCreatedAt()
 * @method string getUpdatedAt()
 */
class CustomerCoupon extends \Magento\Framework\Model\AbstractModel
{

    const ENTITY = 'magecom_customer_salesrule_coupon_relation';

    /**
     * @var \Magento\SalesRule\Model\Rule
     */
    private $salesRule;

    /**
     * @var \Magento\SalesRule\Model\Coupon
     */
    private $coupon;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * CustomerCoupon constructor.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->objectManager = $objectManager;
    }


    /**
     * @return \Magento\SalesRule\Model\Coupon
     */
    public function getCoupon()
    {
        if ($this->coupon === null) {
            /** @var \Magento\SalesRule\Model\ResourceModel\Coupon\Collection $couponCollection */
            $couponCollection = $this->objectManager->create(\Magento\SalesRule\Model\ResourceModel\Coupon\Collection::class);
            $couponCollection->addFieldToFilter('code', $this->getCouponCode());
            $this->coupon = $couponCollection->getFirstItem();
        }
        return $this->coupon;
    }

    /**
     * @return \Magento\SalesRule\Model\Rule
     */
    public function getSalesRule()
    {
        if ($this->salesRule === null) {
            $ruleId = $this->getCoupon()->getRuleId();
            /** @var \Magento\SalesRule\Model\RuleRepository $ruleRepository */
            $ruleRepository = $this->objectManager->create(\Magento\SalesRule\Model\RuleRepository::class);
            $this->salesRule = $ruleRepository->getById($ruleId);
        }
        return $this->salesRule;
    }

    public function isActive()
    {
        $coupon = $this->getCoupon();
        //either unlimited or usage limit is greater then times used
        return $coupon->getUsageLimit() === null || $coupon->getUsageLimit() > $coupon->getTimesUsed();
    }

    /**
     * @return string
     */
    public function getDiscountAmount()
    {
        $discountAmount = $this->getSalesRule()->getDiscountAmount();
        $format = fmod($discountAmount, 1) === 0 ? '%d%%' : '%.2f%%';
        return sprintf($format, $discountAmount);
    }

    /**
     * Model construct that should be used for object initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Magecom\ReviewDiscount\Model\ResourceModel\CustomerCoupon::class);
    }

}