<?php

namespace Magecom\BorrowHome\Model;

use Exception;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Model\Customer;
use Magento\Quote\Model\QuoteRepository;

/**
 * Class OrderHelper
 * @package Magecom\BorrowHome\Helper
 */
class OrderRepository
{

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    private $customerFactory;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerApiRepository;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $cartRepositoryInterface;

    /**
     * @var \Magento\Quote\Model\QuoteManagement
     */
    private $cartManagementInterface;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    private $quote;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer
     */
    private $customerResourceModel;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order
     */
    private $orderRepository;

    /**
     * @var \Magento\Shipping\Model\Config
     */
    private $shippingConfig;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Quote\Model\Quote\Address\Rate
     */
    private $rate;

    /**
     * OrderHelper constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerApiRepository
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface
     * @param \Magento\Quote\Api\CartManagementInterface $cartManagementInterface
     * @param \Magento\Quote\Model\QuoteFactory $quote
     * @param ProductRepositoryInterface $productRepository
     * @param \Magento\Customer\Model\ResourceModel\Customer $customerResourceModel
     * @param \Magento\Sales\Model\ResourceModel\Order $orderRepository
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Shipping\Model\Config $shippingConfig
     * @param \Magento\Quote\Model\Quote\Address\Rate $rate
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerApiRepository,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface,
        \Magento\Quote\Api\CartManagementInterface $cartManagementInterface,
        \Magento\Quote\Model\QuoteFactory $quote,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Customer\Model\ResourceModel\Customer $customerResourceModel,
        \Magento\Sales\Model\ResourceModel\Order $orderRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Shipping\Model\Config $shippingConfig,
        \Magento\Quote\Model\Quote\Address\Rate $rate
    )
    {
        $this->storeManager = $storeManager;
        $this->customerFactory = $customerFactory;
        $this->customerApiRepository = $customerApiRepository;
        $this->cartRepositoryInterface = $cartRepositoryInterface;
        $this->cartManagementInterface = $cartManagementInterface;
        $this->quote = $quote;
        $this->productRepository = $productRepository;
        $this->customerResourceModel = $customerResourceModel;
        $this->orderRepository = $orderRepository;
        $this->scopeConfig = $scopeConfig;
        $this->shippingConfig = $shippingConfig;
        $this->rate = $rate;
    }

    public function create($data)
    {
        $items = $data['items'];

        //init the store id and website id
        /** @var \Magento\Store\Model\Store $store */
        $store = $this->storeManager->getStore();

        //init the customer
        $customer = $this->ensureCustomer($data);
        // if you have already a customer id, then you can load customer directly
        $customer = $this->customerApiRepository->getById($customer->getEntityId());

        $quote = $this->quote->create(); //Create object of quote
        $quote->setStore($store); //set store for which you create quote

        $quote->setCurrency();
        $quote->assignCustomer($customer); //Assign quote to customer

        //add item in quote
        foreach ($items as $productId) {
            $product = $this->productRepository->getById($productId);
            $quote->addProduct($product, 1);
        }
        $this->cartRepositoryInterface->save($quote); // Add this

        //set shipping address from customer to array
        $shippingAddressData = array_merge($data['shipping_address'], [
            'firstname' => $customer->getFirstname(),
            'lastname' => $customer->getLastname(),
            'save_in_address_book' => true,
        ]);

        $shippingMethod = $this->ensureShippingMethod();

        $rate = $this->rate;
        $rate->setCode($shippingMethod);

        //set address to quote
        $quote->getBillingAddress()->addData($shippingAddressData);
        //set address to quote
        $quote->getShippingAddress()->addData($shippingAddressData);
        // collect rates and set shipping method
        $quote->getShippingAddress()->addShippingRate($rate);
        // set payment method
        $quote->getShippingAddress()->setShippingMethod($shippingMethod); //shipping method

        //TODO_EO_BE looks like hard code
        $quote->getPayment()->setMethod('checkmo');


        //now save quote and your quote is ready
        //TODO_EO_BE does not work with QuoteRepository
        /** @noinspection PhpDeprecationInspection */
        $quote->save();

        // Set Sales Order Payment
        $quote->getPayment()->importData(['method' => 'checkmo']);

        // Collect Totals & Save Quote
        $quote->collectTotals();

        // create order from quote
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->cartRepositoryInterface->get($quote->getId());

        // finally submit quote and create new order
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->cartManagementInterface->submit($quote);
        $order->setEmailSent(0);
        $order->setStatus(OrderStatusInterface::ORDER_STATUS_BORROW_HOME);
        $this->orderRepository->save($order);
        return $order;

    }

    /**
     * @param array $data
     * @return Customer
     */
    private function ensureCustomer($data)
    {
        //init the store id and website id
        /** @var \Magento\Store\Model\Store $store */
        $store = $this->storeManager->getStore();
        $websiteId = $this->storeManager->getStore()->getWebsiteId();

        $email = $data['customer']['email'];
        /** @var Customer $customer */
        $customer = $this->customerFactory->create();
        $customer->setWebsiteId($websiteId);
        $customer->loadByEmail($email);
        if (!$customer->getEntityId()) {
            //if not available then create this customer
            $customer->setWebsiteId($websiteId)
                ->setStore($store);
            $customer->setData('firstname', $data['customer']['firstname'])
                ->setData('lastname', $data['customer']['lastname'])
                ->setData('email', $data['customer']['email'])
                ->setPassword($data['customer']['email']);
            /** @noinspection PhpParamsInspection */
            $this->customerResourceModel->save($customer);
        }
        return $customer;
    }

    /**
     * TODO_EO_BE looks like hard code
     * @return string
     * @throws Exception
     */
    private function ensureShippingMethod()
    {
        return 'freeshipping_freeshipping';
        $activeCarriers = $this->shippingConfig->getActiveCarriers();
        foreach ($activeCarriers as $carrierCode => $carrierModel) {
            if ($carrierMethods = $carrierModel->getAllowedMethods()) {
                foreach ($carrierMethods as $methodCode => $method) {
                    return $carrierCode . '_' . $methodCode;
                }
            }
        }
        throw new Exception('No shipping methods available');
    }
}
