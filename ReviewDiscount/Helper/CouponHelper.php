<?php

namespace Magecom\ReviewDiscount\Helper;

use Magento\Framework\App\Helper\Context;

/**
 * Class CouponHelper
 *
 * @package Magecom\ReviewDiscount\Helper
 */
class CouponHelper extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \Magento\SalesRule\Model\Coupon\Massgenerator
     */
    private $generator;

    /**
     * @var \Magecom\ReviewDiscount\Helper\RulesHelper
     */
    private $rulesHelper;

    /**
     * @var \Magecom\ReviewDiscount\Model\CustomerCouponFactory
     */
    private $customerCouponFactory;

    /**
     * @param Context $context
     * @param \Magento\SalesRule\Model\Coupon\Massgenerator $couponCodeGenerator
     * @param \Magecom\ReviewDiscount\Helper\RulesHelper $rulesHelper
     * @param \Magecom\ReviewDiscount\Model\CustomerCouponFactory $customerCouponFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\SalesRule\Model\Coupon\Massgenerator $couponCodeGenerator,
        \Magecom\ReviewDiscount\Helper\RulesHelper $rulesHelper,
        \Magecom\ReviewDiscount\Model\CustomerCouponFactory $customerCouponFactory
    )
    {
        parent::__construct($context);
        $this->generator = $couponCodeGenerator;
        $this->rulesHelper = $rulesHelper;
        $this->customerCouponFactory = $customerCouponFactory;
    }

    /**
     * @param int $discount
     * @return string
     */
    public function generateCouponCodeForDiscount($discount)
    {
        $rule = $this->rulesHelper->ensureRuleWithDiscount($discount);
        return $this->generateCouponCode($rule);
    }

    /**
     * @param \Magento\SalesRule\Model\Rule $rule
     * @return string
     */
    public function generateCouponCode(\Magento\SalesRule\Model\Rule $rule)
    {

        $data = [
            'rule_id' => $rule->getRuleId(),
            'qty' => 1,
            'length' => 8,
            'format' => \Magento\SalesRule\Helper\Coupon::COUPON_FORMAT_ALPHABETICAL,
            'prefix' => 'REVIEW-' . round($rule->getDiscountAmount()) . '-',
            'suffix' => '',
            'dash' => 4,
            'uses_per_coupon' => 1,
        ];

        //no need to validate date which has been set in code
        //if ($this->generator->validateData($data)) {
        $this->generator->setData($data);
        $this->generator->generatePool();
        $codes = $this->generator->getGeneratedCodes();
        return $codes[0];
        //}
    }

    /**
     * @param \Magento\Customer\Model\Customer $customer
     * @param string $couponCode
     */
    public function appendCouponCode(\Magento\Customer\Model\Customer $customer, $couponCode)
    {
        /** @var \Magecom\ReviewDiscount\Model\CustomerCoupon $customerCoupon */
        $customerCoupon = $this->customerCouponFactory->create();
        $customerCoupon->setCustomerEntityId($customer->getEntityId());
        $customerCoupon->setCouponCode($couponCode);
        $customerCoupon->getResource()->save($customerCoupon);
    }
}