<?php

namespace Magecom\BorrowHome\Helper;

use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class ConfigHelper
 * @package Magecom\Consignor\Helper
 */
class ConfigHelper extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var string
     */
    private $freePlaceImageUrl;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        parent::__construct($context);
        $this->storeManager = $storeManager;
    }

    /**
     * @return string
     */
    public function getFreePlaceImageUrl()
    {
        if (is_null($this->freePlaceImageUrl)) {
            /** @noinspection PhpUndefinedMethodInspection */
            $borrowHomeDir = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . 'borrow_home/';
            $this->freePlaceImageUrl = $borrowHomeDir . $this->scopeConfig->getValue('borrow_home/borrow_home/free_place_image', ScopeInterface::SCOPE_STORE);
        }
        return $this->freePlaceImageUrl;
    }
}