<?php

namespace Magecom\LandingPage\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class ParseCategoryUrlObserver
 * @package Magecom\LandingPage\Observer
 */
class ParseCategoryUrlObserver implements ObserverInterface
{
    /**
     * @var \Magecom\LandingPage\Helper\CmsHelper
     */
    private $cmsHelper;

    /**
     * OnDeleteSetNullObserver constructor.
     * @param \Magecom\LandingPage\Helper\CmsHelper $cmsHelper
     */
    public function __construct(\Magecom\LandingPage\Helper\CmsHelper $cmsHelper)
    {
        $this->cmsHelper = $cmsHelper;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Cms\Model\Page $cmsPage */
        $cmsPage = $observer->getData('data_object');

        if ($cmsPage->getData('category_id')) {
            $query = $this->cmsHelper->generateQuery($cmsPage->getData('category_url'));
            $cmsPage->setData('category_query', $query);
        }
    }

}