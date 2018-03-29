<?php

namespace Magecom\ReviewDiscount\Model\ResourceModel\DiscountRule;

/**
 * Class Collection
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
            \Magecom\ReviewDiscount\Model\DiscountRule::class,
            \Magecom\ReviewDiscount\Model\ResourceModel\DiscountRule::class
        );
    }

    /**
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray()['items']);
    }

}