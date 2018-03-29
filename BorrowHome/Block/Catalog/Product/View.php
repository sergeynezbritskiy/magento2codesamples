<?php

namespace Magecom\BorrowHome\Block\Catalog\Product;

/**
 * Class View
 * @package Magecom\BorrowHome\Block\Catalog\Product
 */
class View extends \Magento\Catalog\Block\Product\AbstractProduct
{

    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    private $formKey;

    /**
     * @var \Magecom\BorrowHome\Helper\ProductHelper
     */
    private $productHelper;

    /**
     * View constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Data\Form\FormKey $formKey
     * @param \Magecom\BorrowHome\Helper\ProductHelper $productHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magecom\BorrowHome\Helper\ProductHelper $productHelper,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->formKey = $formKey;
        $this->productHelper = $productHelper;
    }


    /**
     * @return string
     */
    public function getBorrowHomeUrl()
    {
        return $this->getUrl('borrow-home/cart/add', [
            'product' => $this->getProduct()->getEntityId(),
            'uenc' => $this->formKey->getFormKey(),
        ]);
    }

    /**
     * @return bool
     */
    public function borrowHomeAvailable()
    {
        return $this->productHelper->isAvailableForBorrowHome($this->getProduct());
    }
}