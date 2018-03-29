<?php

namespace Magecom\LandingPage\Helper;

use Aheadworks\Layerednav\Model\Config\Source\SeoFriendlyUrl;
use Aheadworks\Layerednav\Model\Url\ConverterPool;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory as PageCollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class CmsHelper
 *
 * @package Magecom\LandingPage\Helper
 */
class CmsHelper
{

    /**
     * @var PageCollectionFactory
     */
    private $pageCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var \Magento\Catalog\Model\Category
     */
    private $currentCategory;

    /**
     * @var \Magento\Cms\Model\Page[]
     */
    private $landingPages = [];
    /**
     * @var \Aheadworks\Layerednav\Model\Url\ConverterPool
     */
    private $converter;

    /**
     * CmsHelper constructor.
     *
     * @param PageCollectionFactory $pageCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param Registry $registry
     * @param RequestInterface $request
     * @param \Aheadworks\Layerednav\Model\Url\ConverterPool $converterPool
     */
    public function __construct(PageCollectionFactory $pageCollectionFactory, StoreManagerInterface $storeManager, Registry $registry, RequestInterface $request, ConverterPool $converterPool)
    {
        $this->pageCollectionFactory = $pageCollectionFactory;
        $this->storeManager = $storeManager;
        $this->registry = $registry;
        $this->request = $request;
        $this->converter = $converterPool->getConverter(
            SeoFriendlyUrl::ATTRIBUTE_VALUE_INSTEAD_OF_ID,
            SeoFriendlyUrl::DEFAULT_OPTION
        );
    }

    /**
     * @param string $url
     * @return string
     */
    public function generateQuery($url)
    {
        $result = [];
        $data = parse_url($url);
        if (array_key_exists('query', $data)) {
            parse_str($data['query'], $result);
        }
        $result = $this->converter->convertFilterParams($result);
        return $this->stringify($result);
    }

    /**
     * @return bool
     */
    public function isLandingPage()
    {
        $params = $this->request->getParams();
        $category = $this->getCurrentCategory();
        return $category && !$this->getPageByCategory($category, $params)->isEmpty();
    }

    /**
     * @param \Magento\Catalog\Model\Category $category
     * @param array $params
     * @return \Magento\Cms\Model\Page|\Magento\Framework\DataObject
     */
    public function getPageByCategory(\Magento\Catalog\Model\Category $category, array $params)
    {
        $params = $this->filterParams($params);
        $categoryQuery = $this->stringify($params);
        $categoryId = $category->getEntityId();

        $cacheKey = $categoryId . $categoryQuery;
        if (!array_key_exists($cacheKey, $this->landingPages)) {
            $pagesCollection = $this->pageCollectionFactory->create();
            $pagesCollection->addFieldToFilter('category_id', $categoryId);
            $pagesCollection->addFieldToFilter('category_query', $categoryQuery);
            /** @noinspection PhpUndefinedMethodInspection */
            $pagesCollection->addFieldToFilter('store_id', ['in' => [0, $this->storeManager->getStore()->getStoreId()]]);
            $this->landingPages[$cacheKey] = $pagesCollection->getFirstItem();
        }
        return $this->landingPages[$cacheKey];
    }

    /**
     * @param array $params
     * @return array
     */
    private function filterParams(array $params)
    {
        $excludeParams = ['id', 'SID', '__store'];
        return array_filter($params, function (&$item) use ($excludeParams) {
            return !in_array($item, $excludeParams);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * @param array $data
     * @return string
     */
    private function stringify(array $data)
    {
        asort($data);
        $result = '';
        foreach ($data as $key => $value) {
            //TODO_EO_BE add support for multiselect
            if (is_string($value)) {
                $result .= '/' . $key . '/' . $value;
            }
        }
        return $result;
    }

    /**
     * @return \Magento\Catalog\Model\Category
     */
    private function getCurrentCategory()
    {
        if ($this->currentCategory === null) {
            $this->currentCategory = $this->registry->registry('current_category');
        }
        return $this->currentCategory;
    }

}