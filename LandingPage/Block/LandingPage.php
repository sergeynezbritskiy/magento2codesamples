<?php

namespace Magecom\LandingPage\Block;

/**
 * Class LandingPage
 * @package Magecom\LandingPage\Block
 */
class LandingPage extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Cms\Model\Page
     */
    private $cmsPage;

    /**
     * @var \Magecom\LandingPage\Helper\CmsHelper
     */
    private $cmsHelper;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    private $filterProvider;

    /**
     * LandingPage constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magecom\LandingPage\Helper\CmsHelper $cmsHelper
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magecom\LandingPage\Helper\CmsHelper $cmsHelper,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->cmsHelper = $cmsHelper;
        $this->registry = $registry;
        $this->filterProvider = $filterProvider;
    }

    /**
     * @return \Magento\Cms\Model\Page
     */
    public function getCmsPage()
    {
        if ($this->cmsPage === null) {
            $params = $this->getRequest()->getParams();
            $category = $this->getCurrentCategory();
            $this->cmsPage = $this->cmsHelper->getPageByCategory($category, $params);
        }
        return $this->cmsPage;
    }

    /**
     * @param string $str
     * @return string
     */
    public function filter($str)
    {
        return $this->filterProvider->getPageFilter()->filter($str);
    }

    /**
     * Retrieve current category model object
     *
     * @return \Magento\Catalog\Model\Category
     */
    private function getCurrentCategory()
    {
        if (!$this->hasData('current_category')) {
            $this->setData('current_category', $this->registry->registry('current_category'));
        }
        return $this->getData('current_category');
    }

}