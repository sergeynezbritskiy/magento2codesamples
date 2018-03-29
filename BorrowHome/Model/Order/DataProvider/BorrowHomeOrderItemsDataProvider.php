<?php

namespace Magecom\BorrowHome\Model\Order\DataProvider;

use Magecom\Extraoptical\Model\ProductAttributes;
use Magecom\OrderPreparation\Model\Order\DataProvider\AbstractDataProvider;

/**
 * Class NorwayOrderItemsDataProvider
 *
 * @package Magecom\OrderPreparation\Model\Order\DataProvider
 */
class BorrowHomeOrderItemsDataProvider extends AbstractDataProvider implements ProductAttributes
{

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    private $productRepository;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    private $productFactory;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    private $priceHelper;

    /**
     * @var \Magecom\Extraoptical\Helper\ProductHelper
     */
    private $productHelper;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    private $directoryList;

    /**
     * @var \Magento\Framework\Filesystem
     */
    private $fileSystem;

    /**
     * PHP 5 allows developers to declare constructor methods for classes.
     * Classes which have a constructor method call this method on each newly-created object,
     * so it is suitable for any initialization that the object may need before it is used.
     *
     * Note: Parent constructors are not called implicitly if the child class defines a constructor.
     * In order to run a parent constructor, a call to parent::__construct() within the child constructor is required.
     *
     * param [ mixed $args [, $... ]]
     *
     * @param \Magento\Sales\Model\Order $order
     * @param \Magento\Catalog\Model\ResourceModel\Product $productRepository
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param \Magecom\Extraoptical\Helper\ProductHelper $productHelper
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Magento\Framework\Filesystem $filesystem
     * @link http://php.net/manual/en/language.oop5.decon.php
     */
    public function __construct(
        \Magento\Sales\Model\Order $order,
        \Magento\Catalog\Model\ResourceModel\Product $productRepository,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magecom\Extraoptical\Helper\ProductHelper $productHelper,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem $filesystem
    )
    {
        parent::__construct($order);
        $this->productRepository = $productRepository;
        $this->productFactory = $productFactory;
        $this->priceHelper = $priceHelper;
        $this->productHelper = $productHelper;
        $this->directoryList = $directoryList;
        $this->fileSystem = $filesystem;
    }


    /**
     * @return array
     */
    public function getData()
    {
        $order = $this->getOrder();
        $result = [];
        $increment = 1;

        $result[] = [
            'Product',
            'Image',
            'Name',
            'Sku',
            'Color variation',
            'Suitable for Progressive',
            'Size',
            'Price',
            'QR-Code',
        ];

        $orderItems = $this->ensureOrderItems($order);
        /** @var \Magento\Sales\Model\Order\Item $item */
        foreach ($orderItems as $item) {
            $product = $item->getProduct();

            $image = $this->ensureImage($product);

            $result[] = [
                'increment' => $increment,
                'image' => $image,
                'name' => $product->getName(),
                'sku' => $product->getSku(),
                'color' => $product->getAttributeText('color'),
                'progressive' => $product->getAttributeText(ProductAttributes::PRODUCT_ATTRIBUTE_LENS_HEIGHT) >= 30 ? 'Yes' : 'No',
                'size' => $product->getAttributeText(ProductAttributes::PRODUCT_ATTRIBUTE_SIZE),
                'price' => $product->getPrice(),
                'qr-code' => $this->getQrCode($product),
            ];
            $increment++;
        }
        //continue increment up to 4
        for (; $increment <= 4; $increment++) {
            $result[] = [
                'increment' => $increment,
                'image' => 'N/A',
            ];
        }
        $transposedResult = $this->transpose($result);

        //all items has numeric keys, but we need to call them within
        //twig so add some name to them, and call like items[0]
        array_walk($transposedResult, function (&$item) {
            $item = ['items' => $item];
        });
        return $transposedResult;
    }

    /**
     * @param array $array
     * @return array
     */
    private function transpose($array)
    {
        return array_map(null, ...$array);
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return \Magento\Sales\Model\Order\Item[]
     */
    private function ensureOrderItems($order)
    {
        $orderItems = $order->getItems();
        return array_filter($orderItems, function ($item) {
            /** @var \Magento\Sales\Model\Order\Item $item */
            return !$item->getParentItemId();
        });
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    private function getQrCode(\Magento\Catalog\Model\Product $product)
    {
        $productUrl = $product->setStoreId(1)->getUrlInStore();
        $productId = $product->getEntityId();
        $filePath = $this->directoryList->getPath('var') . "/qr_codes/$productId.png";
        if (!file_exists($filePath)) {
            require_once dirname(dirname(dirname(__DIR__))) . '/lib/phpqrcode/qrlib.php';
            \QRcode::png($productUrl, $filePath, QR_ECLEVEL_L, 5);
        }
        return $filePath;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return null|string
     */
    private function ensureImage(\Magento\Catalog\Model\Product $product)
    {
        $mediaPath = $this->fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath();
        $image = $product->getImage();
        $imageFullPath = $mediaPath . 'catalog/product' . $image;
        return (file_exists($imageFullPath) && !is_dir($imageFullPath)) ? $imageFullPath : '';
    }

}