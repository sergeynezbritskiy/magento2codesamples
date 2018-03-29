<?php

namespace Magecom\BorrowHome\Helper;

/**
 * Class ProductHelper
 * @package Magecom\BorrowHome\Helper
 */
class ProductHelper extends \Magento\Framework\Url\Helper\Data implements \Magecom\BorrowHome\Model\ProductAttributes
{

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    public function isAvailableForBorrowHome(\Magento\Catalog\Model\Product $product)
    {
        return $product->getAttributeText(self::PRODUCT_ATTRIBUTE_BORROW_HOME) === self::BORROW_HOME_ATTRIBUTE_OPTION_AVAILABLE;
    }

}