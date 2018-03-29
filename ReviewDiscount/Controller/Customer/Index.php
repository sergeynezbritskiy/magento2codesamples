<?php

namespace Magecom\ReviewDiscount\Controller\Customer;

/**
 * Class Index
 *
 * @package Magecom\ReviewDiscount\Controller\Customer
 */
class Index extends \Magento\Customer\Controller\AbstractAccount
{

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    /**
     * Index constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory)
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }


    public function execute()
    {
        return $this->resultPageFactory->create();
    }
}