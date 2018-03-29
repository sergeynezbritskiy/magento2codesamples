<?php

namespace Magecom\ReviewDiscount\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class CustomerCoupon
 *
 * @package Magecom\ReviewDiscount\Model\ResourceModel
 */
class CustomerCoupon extends AbstractDb
{

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Magecom\ReviewDiscount\Model\CustomerCoupon::ENTITY, 'entity_id');
    }

}