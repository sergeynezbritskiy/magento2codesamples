<?php

namespace Magecom\BorrowHome\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class BorrowHomeCart
 *
 * @package Magecom\BorrowHome\CustomerData
 */
class BorrowHomeCart extends \Magento\Framework\DataObject implements SectionSourceInterface
{

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Url
     */
    private $catalogUrl;

    /**
     * @var \Magento\Checkout\CustomerData\ItemPoolInterface
     */
    private $itemPoolInterface;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    /**
     * @var \Magecom\BorrowHome\Model\Session
     */
    private $borrowHomeSession;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var int
     */
    private $storeId;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    private $imageHelper;

    /**
     * @var \Magento\Checkout\Helper\Data
     */
    private $checkoutHelper;

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Url $catalogUrl
     * @param \Magento\Checkout\CustomerData\ItemPoolInterface $itemPoolInterface
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param \Magecom\BorrowHome\Model\Session $borrowHomeSession
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Checkout\Helper\Data $checkoutHelper
     * @param array $data
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Url $catalogUrl,
        \Magento\Checkout\CustomerData\ItemPoolInterface $itemPoolInterface,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magecom\BorrowHome\Model\Session $borrowHomeSession,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Checkout\Helper\Data $checkoutHelper,
        array $data = []
    )
    {
        parent::__construct($data);
        $this->catalogUrl = $catalogUrl;
        $this->itemPoolInterface = $itemPoolInterface;
        $this->layout = $layout;
        $this->borrowHomeSession = $borrowHomeSession;
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
        $this->imageHelper = $imageHelper;
        $this->checkoutHelper = $checkoutHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getSectionData()
    {
        return [
            'summary_count' => $this->getSummaryCount(),
            'items' => $this->getRecentItems(),
            'extra_actions' => $this->layout->createBlock('Magento\Catalog\Block\ShortcutButtons')->toHtml(),
        ];
    }

    /**
     * Get shopping cart items qty based on configuration (summary qty or items qty)
     *
     * @return int|float
     */
    private function getSummaryCount()
    {
        return count($this->borrowHomeSession->getProducts());
    }

    /**
     * Get array of last added items
     *
     * @return \Magento\Quote\Model\Quote\Item[]
     */
    private function getRecentItems()
    {
        $items = [];

        $itemsData = $this->borrowHomeSession->getProducts();
        foreach ($itemsData as $itemData) {
            //TODO_EO_BE add support for configurable products
            $productId = $itemData['product_id'];
            try {
                /** @var \Magento\Catalog\Model\Product $product */
                $product = $this->productRepository->getById($productId);
            } catch (NoSuchEntityException $ignore) {
                continue;
            }
            if (!$product->isVisibleInSiteVisibility()) {
                $products = $this->catalogUrl->getRewriteByProductStore([$product->getId() => $this->getStoreId()]);
                if (!isset($products[$product->getId()])) {
                    continue;
                }
            }
            $items[] = $this->getProductData($product);
        }
        return $items;
    }

    /**
     * @return int
     */
    private function getStoreId()
    {
        if ($this->storeId === null) {
            /** @noinspection PhpUndefinedMethodInspection */
            $this->storeId = $this->storeManager->getStore()->getStoreId();
        }
        return $this->storeId;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    private function getProductData(\Magento\Catalog\Model\Product $product)
    {
        $imageHelper = $this->imageHelper->init($product, 'mini_cart_product_thumbnail');
        /** @noinspection PhpUndefinedMethodInspection */
        return [
            'qty' => 1,
            'is_visible_in_site_visibility' => $product->isVisibleInSiteVisibility(),
            'product_name' => $product->getName(),
            'product_sku' => $product->getSku(),
            'product_url' => $product->getUrlModel()->getUrl($product),
            'product_has_url' => true,
            'remove_product_url' => $this->storeManager->getStore()->getUrl('borrow_home/cart/remove', [
                'product_id' => $product->getEntityId(),
            ]),
            'product_price' => $this->checkoutHelper->formatPrice($product->getFinalPrice()),
            'product_price_value' => $product->getFinalPrice(),
            'product_image' => [
                'src' => $imageHelper->getUrl(),
                'alt' => $imageHelper->getLabel(),
                'width' => $imageHelper->getWidth(),
                'height' => $imageHelper->getHeight(),
            ],
        ];
    }

}