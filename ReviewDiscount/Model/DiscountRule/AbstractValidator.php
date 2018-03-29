<?php

namespace Magecom\ReviewDiscount\Model\DiscountRule;

/**
 * Class AbstractValidator
 * @package Magecom\ReviewDiscount\Model\DiscountRule
 */
abstract class AbstractValidator
{

    /**
     * @param $data
     * @return bool
     */
    abstract public function validate($data);
}