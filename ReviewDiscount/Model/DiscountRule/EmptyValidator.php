<?php

namespace Magecom\ReviewDiscount\Model\DiscountRule;

/**
 * Class EmptyValidator
 * @package Magecom\ReviewDiscount\Model\DiscountRule
 */
class EmptyValidator extends AbstractValidator
{

    /**
     * @param $data
     * @return bool
     */
    public function validate($data)
    {
        return !empty($data);
    }

}