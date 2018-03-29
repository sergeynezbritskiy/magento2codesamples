<?php

namespace Magecom\BorrowHome\Controller\Order;

use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magecom\BorrowHome\Model\ValidationException;

/**
 * Class Create
 *
 * @package Magecom\BorrowHome\Controller\Order
 */
class Create extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    private $formKeyValidator;

    /**
     * @var \Magecom\BorrowHome\Model\OrderRepository
     */
    private $orderHelper;

    /**
     * @var \Magecom\BorrowHome\Model\Session
     */
    private $session;

    /**
     * Create constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magecom\BorrowHome\Model\OrderRepository $orderHelper
     * @param \Magecom\BorrowHome\Model\Session $session
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magecom\BorrowHome\Model\OrderRepository $orderHelper,
        \Magecom\BorrowHome\Model\Session $session
    )
    {
        parent::__construct($context);
        $this->formKeyValidator = $formKeyValidator;
        $this->orderHelper = $orderHelper;
        $this->session = $session;
    }


    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|\Magento\Framework\App\ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        /** @noinspection PhpUndefinedMethodInspection */
        $orderData = $this->getRequest()->getPost('BorrowHome');

        try {
            $this->validateData($orderData);

            $items = $this->session->getProducts();
            if (count($items) === 0) {
                throw new LocalizedException(__('Your borrow home cart is empty'));
            }
            $orderData['items'] = array_keys($items);

            /** @var \Magento\Sales\Model\Order $order */
            $order = $this->orderHelper->create($orderData);
            $this->session->flush();
        } catch (ValidationException $e) {
            $resultRedirect->setUrl('/borrow-home/cart/');
            return $resultRedirect;
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $resultRedirect->setUrl('/borrow-home/cart/');
            return $resultRedirect;
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
            $this->messageManager->addErrorMessage(__('We can\'t create your order right now.'));
            $resultRedirect->setUrl('/borrow-home/cart/');
            return $resultRedirect;
        }
        return $this->createSuccessPageRedirect($order);
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    private function createSuccessPageRedirect(\Magento\Sales\Model\Order $order)
    {
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            $this->messageManager->addErrorMessage('Your form key has expired');
            return $this->resultRedirectFactory->create()->setPath('/borrow-home/cart/');
        }
        /** @noinspection PhpUndefinedMethodInspection */
        $this->session
            ->setLastQuoteId($order->getQuoteId())
            ->setLastSuccessQuoteId($order->getQuoteId())
            ->clearHelperData();
        /** @noinspection PhpUndefinedMethodInspection */
        $this->session
            ->setLastOrderId($order->getId())
            ->setRedirectUrl(null)
            ->setLastRealOrderId($order->getIncrementId())
            ->setLastOrderStatus($order->getStatus());

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl('/checkout/onepage/success/');
        return $resultRedirect;
    }

    /**
     * @param $orderData
     * @throws ValidationException
     */
    private function validateData($orderData)
    {
        $requiredFields = [
            'customer' => ['firstname', 'lastname'],
            'shipping_address' => ['city', 'postcode', 'country_id', 'telephone', 'street']
        ];
        $hasErrors = false;
        if (empty($orderData['customer']['email']) || !filter_var($orderData['customer']['email'], FILTER_VALIDATE_EMAIL)) {
            $hasErrors = true;
            $this->messageManager->addWarningMessage(__('Provided email is invalid'));
        }
        foreach ($requiredFields as $groupName => $fields) {
            foreach ($fields as $field) {
                if (empty($orderData[$groupName][$field])) {
                    $hasErrors = true;
                    $this->messageManager->addWarningMessage(__('Field %1 is required', $field));
                }
            }
        }
        if ($hasErrors) {
            throw new ValidationException(__('The data you provided is invalid'));
        }
    }

}