<?php

namespace Magecom\ReviewDiscount\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class DiscountRule
 * @package Magecom\ReviewDiscount\Model\ResourceModel
 */
class DiscountRule extends AbstractDb
{

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Magecom\ReviewDiscount\Model\DiscountRule::ENTITY . '_entity', 'entity_id');
    }

}