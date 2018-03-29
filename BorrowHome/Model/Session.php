<?php

namespace Magecom\BorrowHome\Model;

use Exception;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Session
 *
 * @package Magecom\BorrowHome\Model
 */
class Session extends \Magento\Checkout\Model\Session
{

    const BORROW_HOME_KEY = 'borrow-home';

    const MAX_BORROW_HOME_ITEMS_COUNT = 4;

    /**
     * @param \Magento\Catalog\Model\Product[] $productData
     * @return void
     * @throws LocalizedException
     */
    public function addProduct($productData)
    {
        $sessionData = $this->getProducts();
        $productKey = $this->ensureProductKey($productData);

        $childProduct = null;
        $parentProduct = null;
        foreach ($productData as $product) {
            //key should be always simple product id
            if ($product->getTypeId() === Configurable::TYPE_CODE) {
                $parentProduct = $product;
            } else {
                $childProduct = $product;
            }
        }
        if ($parentProduct === null) {
            //simple product has been added
            $content = [
                'product_id' => $childProduct->getEntityId(),
                'configuration' => null
            ];

        } else {
            //configurable product has been added
            $content = [
                'product_id' => $parentProduct->getEntityId(),
                'configuration' => $childProduct->getEntityId()
            ];
        }
        if (!empty($sessionData[$productKey])) {
            throw new LocalizedException(__('This product is already in your borrow home list'));
        }
        if (count($sessionData) >= self::MAX_BORROW_HOME_ITEMS_COUNT) {
            throw new LocalizedException(__('You can select only %1 items for borrow home', self::MAX_BORROW_HOME_ITEMS_COUNT));
        }

        $sessionData[$productKey] = $content;
        /** @noinspection PhpUndefinedMethodInspection */
        $this->storage->setData($this->getKey(), $sessionData);
    }

    /**
     * @param \Magento\Catalog\Model\Product[] $productData
     * @return void
     */
    public function removeProduct($productData)
    {
        $sessionData = $this->getProducts();
        $productKey = $this->ensureProductKey($productData);
        unset($sessionData[$productKey]);
        $this->setProducts($sessionData);
    }

    /**
     * @return array
     */
    public function getProducts()
    {
        $result = $this->getData($this->getKey()) ?: [];
        return $result;
    }

    /**
     * @return void
     */
    public function flush()
    {
        $this->setProducts([]);
    }

    /**
     * @return string
     */
    protected function getKey()
    {
        return self::BORROW_HOME_KEY;
    }

    /**
     * @param array $sessionData
     */
    private function setProducts($sessionData)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->storage->setData($this->getKey(), $sessionData);
    }

    /**
     * @param \Magento\Catalog\Model\Product[] $productData
     * @return int
     * @throws Exception
     */
    private function ensureProductKey($productData)
    {
        foreach ($productData as $product) {
            /** @var \Magento\Catalog\Model\Product $product */
            //key should be always simple product id
            if ($product->getTypeId() !== Configurable::TYPE_CODE) {
                return $product->getEntityId();
            }
        }
        $msg = 'Unable to get simple product id. Either empty list has been passed or the list with configurable products only';
        throw new Exception(__($msg));
    }

}