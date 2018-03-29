<?php

namespace Magecom\BorrowHome\Block\Cart;

use Magento\Framework\View\Element\Template;
use Magecom\BorrowHome\Model\ProductAttributes;

/**
 * Class EmptyItem
 * @package Magecom\BorrowHome\Block\Cart
 */
class EmptyItem extends \Magento\Framework\View\Element\Template implements ProductAttributes
{

    /**
     * @var \Magecom\BorrowHome\Helper\ConfigHelper
     */
    private $config;

    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    /**
     * @var string
     */
    private $borrowMoreUrl;

    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param \Magecom\BorrowHome\Helper\ConfigHelper $config
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magecom\BorrowHome\Helper\ConfigHelper $config,
        \Magento\Eav\Model\Config $eavConfig,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->config = $config;
        $this->eavConfig = $eavConfig;
    }


    /**
     * @return string
     */
    public function getBorrowMoreUrl()
    {
        if ($this->borrowMoreUrl === null) {
            $eav = $this->eavConfig;
            $attribute = $eav->getAttribute('catalog_product', ProductAttributes::PRODUCT_ATTRIBUTE_BORROW_HOME);
            $borrowHomeId = $attribute->getSource()->getOptionId(ProductAttributes::BORROW_HOME_ATTRIBUTE_OPTION_AVAILABLE);
            //TODO_EO_BE move to config, this is a hardcoded thing
            $this->borrowMoreUrl = sprintf('/eyeglasses.html?borrow_home=%d', $borrowHomeId);
        }
        return $this->borrowMoreUrl;
    }

    /**
     * @return string
     */
    public function getFreePlaceImageUrl()
    {
        return $this->config->getFreePlaceImageUrl();
    }
}