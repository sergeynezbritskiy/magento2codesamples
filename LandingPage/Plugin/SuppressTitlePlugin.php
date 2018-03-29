<?php

namespace Magecom\LandingPage\Plugin;

/**
 * Class SuppressTitlePlugin
 * @package Magecom\LandingPage\Plugin
 */
class SuppressTitlePlugin extends \Magento\Framework\DataObject
{

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Magecom\LandingPage\Helper\CmsHelper
     */
    private $cmsHelper;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * SuppressTitlePlugin constructor.
     * @param \Magento\Framework\Registry $registry
     * @param \Magecom\LandingPage\Helper\CmsHelper $cmsHelper
     * @param \Magento\Framework\App\RequestInterface $request
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magecom\LandingPage\Helper\CmsHelper $cmsHelper,
        \Magento\Framework\App\RequestInterface $request,
        array $data = []
    )
    {
        parent::__construct($data);
        $this->registry = $registry;
        $this->cmsHelper = $cmsHelper;
        $this->request = $request;
    }

    /**
     * @param \Magento\Theme\Block\Html\Title $subject
     * @param $result
     * @return string
     */
    public function afterGetPageHeading(\Magento\Theme\Block\Html\Title $subject, $result)
    {
        return $this->cmsHelper->isLandingPage() ? '' : $result;
    }

}