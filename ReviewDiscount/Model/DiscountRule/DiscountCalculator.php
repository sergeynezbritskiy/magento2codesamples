<?php

namespace Magecom\ReviewDiscount\Model\DiscountRule;

use Exception;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magecom\ReviewDiscount\Model\DiscountRule;

/**
 * Class DiscountCalculator
 * @package Magecom\ReviewDiscount\Model\DiscountRule
 */
class DiscountCalculator
{
    /**
     * @var \Magecom\ReviewDiscount\Model\DiscountRuleFactory
     */
    private $discountRuleFactory;

    /**
     * @var \Magecom\ReviewDiscount\Model\ResourceModel\DiscountRule\Collection
     */
    private $collection;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * DiscountCalculator constructor.
     *
     * @param \Magecom\ReviewDiscount\Model\DiscountRuleFactory $discountRuleFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(
        \Magecom\ReviewDiscount\Model\DiscountRuleFactory $discountRuleFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    )
    {
        $this->discountRuleFactory = $discountRuleFactory;
        $this->productRepository = $productRepository;
    }

    /**
     * @param array $data
     * @return float
     * @throws Exception
     */
    public function calculate($data)
    {
        $discount = 0;
        $collection = $this->getCollection();
        /** @var DiscountRule $discountRule */
        foreach ($collection as $discountRule) {
            if ($discountRule->isApplicable($data, $collection)) {
                $discount += $discountRule->getDiscount();
            }
        }

        //destroy collection in order to use this class for further calculations
        $this->collection = null;
        return $discount;
    }

    /**
     * @return \Magecom\ReviewDiscount\Model\ResourceModel\DiscountRule\Collection
     */
    public function getCollection()
    {
        if (is_null($this->collection)) {
            /** @var \Magecom\ReviewDiscount\Model\ResourceModel\DiscountRule\Collection $collection */
            $this->collection = $this->discountRuleFactory->create()->getCollection();
            $this->collection->addFieldToFilter('enabled', ['eg' => 1]);
        }
        return $this->collection;
    }

    /**
     * @return string
     */
    public function toJson()
    {
        return $this->getCollection()->toJson();
    }

    /**
     * @param \Magento\Review\Model\Review $review
     * @return float
     */
    public function calculateForReview(\Magento\Review\Model\Review $review)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $ratingVotes = count($review->getRatings());
        return $this->calculate(array_merge($review->toArray(), [
            'rating' => $ratingVotes
        ]));
    }
}