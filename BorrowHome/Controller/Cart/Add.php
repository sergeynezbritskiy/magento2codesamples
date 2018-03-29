<?php

namespace Magecom\BorrowHome\Controller\Cart;

/**
 * Class Add
 *
 * This class is partly a copy/paste version
 * of class \Magento\Checkout\Controller\Cart\Add
 *
 * @package Magecom\BorrowHome\Controller\Cart
 */
class Add extends \Magento\Checkout\Controller\Cart
{

    /**
     * @override
     * @var \Magecom\BorrowHome\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Magecom\BorrowHome\Helper\ProductHelper
     */
    private $productHelper;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magecom\BorrowHome\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magecom\BorrowHome\Helper\ProductHelper $productHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magecom\BorrowHome\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magecom\BorrowHome\Helper\ProductHelper $productHelper
    )
    {
        parent::__construct($context, $scopeConfig, $checkoutSession, $storeManager, $formKeyValidator, $cart);
        $this->productRepository = $productRepository;
        $this->checkoutSession = $checkoutSession;
        $this->productHelper = $productHelper;
    }

    /**
     * Initialize product instance from request data
     *
     * @return \Magento\Catalog\Model\Product|\Magento\Catalog\Api\Data\ProductInterface|false
     */
    protected function _initProduct()
    {
        $productId = (int)$this->getRequest()->getParam('product');
        if ($productId) {
            $storeId = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId();
            try {
                return $this->productRepository->getById($productId, false, $storeId);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                return false;
            }
        }
        return false;
    }

    /**
     * Add product to shopping cart action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            $this->messageManager->addErrorMessage(__('Your form has been expired'));
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        $params = $this->getRequest()->getParams();
        $product = $this->_initProduct();
        try {
            if (isset($params['qty'])) {
                $filter = new \Zend_Filter_LocalizedToNormalized(
                    ['locale' => $this->_objectManager->get('Magento\Framework\Locale\ResolverInterface')->getLocale()]
                );
                $params['qty'] = $filter->filter($params['qty']);
            }


            /**
             * Check product availability
             */
            if (!$product) {
                return $this->_goBack();
            }

            $candidate = $product->getTypeInstance()->prepareForCartAdvanced(
                new \Magento\Framework\DataObject($params), $product);

            /**
             * Error message
             */
            if (is_string($candidate)) {
                throw new \Magento\Framework\Exception\LocalizedException(__($candidate));
            } elseif (!is_array($candidate)) {
                $candidate = [$candidate];
            }

            //TODO_EO_BE probably have to check configuration too, not only parent product
            if (!$this->productHelper->isAvailableForBorrowHome($product)) {
                throw new \Magento\Framework\Exception\LocalizedException(__('This product is not available for borrow home'));
            }

            /**
             * Start saving product for borrow home
             */
            $this->checkoutSession->addProduct($candidate);
            /**
             * End saving product for borrow home
             */

            $message = __('You added %1 to your borrow home list.',$product->getName());
            $this->messageManager->addSuccessMessage($message);
            $returnUrl = $product->getProductUrl();
            return $this->resultRedirectFactory->create()->setPath($returnUrl);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {

            $this->messageManager->addNoticeMessage(
                $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($e->getMessage())
            );

            /** @noinspection PhpUndefinedMethodInspection */
            $url = $product ? $product->getProductUrl() : $this->_checkoutSession->getRedirectUrl(true);

            return $this->_goBack($url);

        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('We can\'t add this item to your borrow home list right now.'));
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            return $product ? $this->resultRedirectFactory->create()->setPath($product->getProductUrl()) : $this->_goBack();
        }
    }

}
