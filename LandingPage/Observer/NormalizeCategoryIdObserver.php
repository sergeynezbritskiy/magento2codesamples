<?php

namespace Magecom\LandingPage\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class NormalizeCategoryIdObserver
 *
 * @package Magecom\LandignPage\Observer
 */
class NormalizeCategoryIdObserver implements ObserverInterface
{

    /**
     * @param Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Cms\Model\Page $cmsPage */
        $cmsPage = $observer->getData('data_object');
        if (empty($cmsPage->getData('category_id'))) {
            $cmsPage->setData('category_id', null);
        }
    }

}