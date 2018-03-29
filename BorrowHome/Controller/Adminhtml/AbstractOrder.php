<?php

namespace Magecom\BorrowHome\Controller\Adminhtml;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Phrase;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Status;

/**
 * Class AbstractOrder
 * @package Magecom\BorrowHome\Controller\Adminhtml
 */
abstract class AbstractOrder extends \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction implements \Magecom\BorrowHome\Model\OrderStatusInterface
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Status
     */
    private $statusRepository;
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order
     */
    private $orderRepository;
    /**
     * @var \Magento\Sales\Model\Order\StatusFactory
     */
    private $statusFactory;
    /**
     * @var \Magento\Sales\Api\OrderManagementInterface
     */
    private $orderManagement;

    /**
     * @var Status
     */
    private $newStatus;

    /**
     * @var Status
     */
    private $oldStatus;

    /**
     * This status should be set
     * @return string
     */
    abstract protected function getNewOrderStatusKey();

    /**
     * This state should be set
     * @return string
     */
    abstract protected function getNewOrderStateKey();

    /**
     * This status should be changed from
     * @return string
     */
    abstract protected function getOldOrderStatusKey();


    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $collectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Status $statusRepository
     * @param \Magento\Sales\Model\ResourceModel\Order $orderRepository
     * @param \Magento\Sales\Model\Order\StatusFactory $statusFactory
     * @param \Magento\Sales\Api\OrderManagementInterface $orderManagement
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $collectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Status $statusRepository,
        \Magento\Sales\Model\ResourceModel\Order $orderRepository,
        \Magento\Sales\Model\Order\StatusFactory $statusFactory,
        \Magento\Sales\Api\OrderManagementInterface $orderManagement
    )
    {
        parent::__construct($context, $filter);
        $this->request = $request;
        $this->collectionFactory = $collectionFactory;
        $this->statusRepository = $statusRepository;
        $this->orderRepository = $orderRepository;
        $this->statusFactory = $statusFactory;
        $this->orderManagement = $orderManagement;
    }


    /**
     * Set status to collection items
     *
     * @param AbstractCollection $collection
     * @return ResponseInterface|ResultInterface
     */
    protected function massAction(AbstractCollection $collection)
    {
        $ordersAffectedCount = 0;
        $ordersDeclinedCount = 0;
        /** @var Order $order */
        foreach ($collection->getItems() as $order) {
            try {
                $this->setNewStatus($order);
                $ordersAffectedCount++;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $ordersDeclinedCount++;
            }
        }
        if ($ordersDeclinedCount > 0) {
            $this->messageManager->addErrorMessage(__('%1 orders have not been updated', $ordersDeclinedCount));
        }
        if ($ordersAffectedCount > 0) {
            $this->messageManager->addSuccessMessage(__('%1 orders have been successfully updated', $ordersAffectedCount));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('sales/order');
        return $resultRedirect;
    }

    /**
     * @param Order $order
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function setNewStatus(Order $order)
    {
        $newStatus = $this->getNewStatus();
        $oldStatus = $this->getOldStatus();
        if ($order->getStatus() !== $oldStatus->getStatus()) {
            $msg = __(sprintf('Can\'t change status from %s to %s', $this->getOldOrderStatusKey(), $this->getNewOrderStatusKey()));
            throw new \Magento\Framework\Exception\LocalizedException($msg);
        }
        $order->setStatus($newStatus->getStatus())->setState($this->getNewOrderStateKey());
        $this->orderRepository->save($order);
    }

    /**
     * @return Status
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getNewStatus()
    {
        if ($this->newStatus === null) {
            $this->newStatus = $this->getStatusByKey($this->getNewOrderStatusKey());
        }
        return $this->newStatus;
    }

    /**
     * @return Status
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getOldStatus()
    {
        if ($this->oldStatus === null) {
            $this->oldStatus = $this->getStatusByKey($this->getOldOrderStatusKey());
        }
        return $this->oldStatus;
    }

    /**
     * @param string $key
     * @return Status
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getStatusByKey($key)
    {
        $status = $this->statusFactory->create();
        $this->statusRepository->load($status, $key);
        if (!$status->getStatus()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(new Phrase(__('Status ' . $key . ' doesn\'t exist')));
        }
        return $status;
    }
}