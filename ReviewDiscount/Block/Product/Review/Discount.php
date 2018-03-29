<?php

namespace Magecom\ReviewDiscount\Block\Product\Review;

/**
 * Class Discount
 *
 * @package Magecom\ReviewDiscount\Block\Product\Review
 */
class Discount extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magecom\ReviewDiscount\Model\DiscountRule\DiscountCalculator
     */
    private $discountCalculator;

    /**
     * Discount constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magecom\ReviewDiscount\Model\DiscountRule\DiscountCalculator $discountCalculator
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magecom\ReviewDiscount\Model\DiscountRule\DiscountCalculator $discountCalculator,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->discountCalculator = $discountCalculator;
    }

    /**
     * @return \Magecom\ReviewDiscount\Model\DiscountRule\DiscountCalculator
     */
    public function getDiscountCalculator()
    {
        return $this->discountCalculator;
    }

}