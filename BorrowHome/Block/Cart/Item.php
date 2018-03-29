<?php

namespace Magecom\BorrowHome\Block\Cart;

/**
 * Class Item
 *
 * @package Magecom\BorrowHome\Block\Cart
 */
class Item extends \Magento\Catalog\Block\Product\AbstractProduct
{

    /**
     * @var int[]|null
     */
    private $cartItemData;

    /**
     * @var \Magento\Catalog\Model\Product|null
     */
    private $configuration;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    private $product;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * Constructor
     *
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->productRepository = $productRepository;
    }

    /**
     * Retrieve product image
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $imageId
     * @param array $attributes
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function getImage($product, $imageId, $attributes = [])
    {
        if ($this->hasConfiguration() && $this->getConfiguration()->getImage()) {
            //first try to get image for configuration
            $product = $this->getConfiguration();
        }
        return parent::getImage($product, $imageId, $attributes);
    }

    /**
     * @param int[] $cartItemData
     */
    public function setCartItemData(array $cartItemData)
    {
        $this->cartItemData = $cartItemData;
    }

    /**
     * @return int[]
     */
    public function getCartItemData()
    {
        return $this->cartItemData;
    }

    /**
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        if ($this->product === null) {
            $this->product = $this->productRepository->getById($this->cartItemData['product_id']);
        }
        return $this->product;
    }

    /**
     * @return \Magento\Catalog\Model\Product
     */
    public function getConfiguration()
    {
        if ($this->configuration === null && $this->hasConfiguration()) {
            $this->configuration = $this->productRepository->getById($this->cartItemData['configuration']);
        }
        return $this->configuration;
    }

    /**
     * @return bool
     */
    public function hasConfiguration()
    {
        return !empty($this->cartItemData['configuration']);
    }

    /**
     * @return string
     */
    public function getDeleteItemUrl()
    {
        return $this->getUrl('borrow-home/cart/remove', [
            'product_id' => isset($this->cartItemData['configuration']) ? $this->cartItemData['configuration'] : $this->cartItemData['product_id']
        ]);
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return mixed
     */
    public function getManufacturerUrl($product)
    {
        //TODO_EO_BE implement getManufacturerUrl
        return $product->getProductUrl();
    }

}