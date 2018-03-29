<?php

namespace Magecom\BorrowHome\Controller\Cart;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magecom\BorrowHome\Block\Cart\EmptyItem;

/**
 * Class Remove
 * @package Magecom\BorrowHome\Controller\Cart
 */
class Remove extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $jsonFactory;

    /**
     * @var \Magecom\BorrowHome\Model\Session
     */
    private $session;

    /**
     * Addons constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Magecom\BorrowHome\Model\Session $session
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magecom\BorrowHome\Model\Session $session
    )
    {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->session = $session;
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|\Magento\Framework\App\ResponseInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $responseData = array(
            'errors' => array(),
            'content' => '',
            'contentHtml' => ''
        );
        $result = $this->jsonFactory->create();

        try {
            $product = $this->findProduct();
            $this->session->removeProduct([$product]);
            /** @var \Magecom\BorrowHome\Block\Cart\EmptyItem $emptyItemBlock */
            $emptyItemBlock = $this->_view->getLayout()->createBlock(EmptyItem::class);
            $emptyItemBlock->setTemplate('cart/item/empty.phtml');
            $responseData['contentHtml'] = $emptyItemBlock->toHtml();
        } catch (NoSuchEntityException $e) {
            $result->setHttpResponseCode(\Magento\Framework\Webapi\Exception::HTTP_NOT_FOUND);
            $responseData['errors'][] = $e->getMessage();
        } catch (LocalizedException $e) {
            $result->setHttpResponseCode(\Magento\Framework\Webapi\Exception::HTTP_INTERNAL_ERROR);
            $responseData['errors'][] = $e->getMessage();
        } catch (\Exception $e) {
            $result->setHttpResponseCode(\Magento\Framework\Webapi\Exception::HTTP_INTERNAL_ERROR);
            $responseData['errors'][] = __('Internal server error');
        }

        return $result->setData($responseData);
    }

    /**
     * @return \Magento\Catalog\Model\Product
     * @throws \Exception
     */
    private function findProduct()
    {
        // Get initial data from request
        $productId = (int)$this->getRequest()->getParam('product_id');

        if (!$productId) {
            throw new \Exception('Product id is invalid');
        }

        /** @var \Magento\Catalog\Api\ProductRepositoryInterface $productRepository */
        $productRepository = $this->_objectManager->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $productRepository->getById($productId);
        return $product;
    }

}