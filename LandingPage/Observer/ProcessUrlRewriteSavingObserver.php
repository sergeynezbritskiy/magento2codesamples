<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magecom\LandingPage\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\CmsUrlRewrite\Model\CmsPageUrlRewriteGenerator;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magecom\LandingPage\Helper\CmsHelper;

class ProcessUrlRewriteSavingObserver implements ObserverInterface
{
    /**
     * @var \Magento\CmsUrlRewrite\Model\CmsPageUrlRewriteGenerator
     */
    protected $cmsPageUrlRewriteGenerator;

    /**
     * @var UrlPersistInterface
     */
    protected $urlPersist;

    /**
     * @var CmsHelper
     */
    private $cmsHelper;

    /**
     * @param \Magento\CmsUrlRewrite\Model\CmsPageUrlRewriteGenerator $cmsPageUrlRewriteGenerator
     * @param UrlPersistInterface $urlPersist
     * @param CmsHelper $cmsHelper
     */
    public function __construct(
        CmsPageUrlRewriteGenerator $cmsPageUrlRewriteGenerator,
        UrlPersistInterface $urlPersist,
        CmsHelper $cmsHelper
    )
    {
        $this->cmsPageUrlRewriteGenerator = $cmsPageUrlRewriteGenerator;
        $this->urlPersist = $urlPersist;
        $this->cmsHelper = $cmsHelper;
    }

    /**
     * Generate urls for UrlRewrite and save it in storage
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        /** @var $cmsPage \Magento\Cms\Model\Page */
        $cmsPage = $observer->getEvent()->getData('object');

        if ($this->dataChanged($cmsPage)) {
            //clear url rewrites first
            $this->urlPersist->deleteByData([
                UrlRewrite::ENTITY_ID => $cmsPage->getId(),
                UrlRewrite::ENTITY_TYPE => CmsPageUrlRewriteGenerator::ENTITY_TYPE,
            ]);

            $urls = $this->cmsPageUrlRewriteGenerator->generate($cmsPage);
            if ($cmsPage->getData('category_id')) {
                $query = $cmsPage->getData('category_query');
                $targetPath = 'catalog/category/view/id/' . $cmsPage->getData('category_id') . $query;
                foreach ($urls as $url) {
                    $url->setTargetPath($targetPath);
                }
            }
            $this->urlPersist->replace($urls);
        }
    }

    /**
     * @param \Magento\Cms\Model\Page $cmsPage
     * @return bool
     */
    private function dataChanged(\Magento\Cms\Model\Page $cmsPage)
    {
        $fields = ['identifier', 'store_id', 'category_id', 'category_url'];
        foreach ($fields as $field) {
            if ($cmsPage->dataHasChangedFor($field)) {
                return true;
            }
        }
        return false;
    }

}
