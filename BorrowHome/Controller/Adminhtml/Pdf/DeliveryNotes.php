<?php

namespace Magecom\BorrowHome\Controller\Adminhtml\Pdf;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Sales\Model\OrderRepository;

/**
 * Class DeliveryNotes
 *
 * @package Magecom\OrderPreparation\Controller\Adminhtml\Pdf
 */
class DeliveryNotes extends \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction
{

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Sales::shipment';

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var \Magecom\OrderPreparation\Model\Order\DeliveryNote
     */
    protected $pdfShipment;

    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    private $orderRepository;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magecom\OrderPreparation\Model\Order\DeliveryNote $shipment
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $collectionFactory
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magecom\OrderPreparation\Model\Order\DeliveryNote $shipment,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $collectionFactory,
        OrderRepository $orderRepository
    )
    {
        parent::__construct($context, $filter);
        $this->fileFactory = $fileFactory;
        $this->dateTime = $dateTime;
        $this->pdfShipment = $shipment;
        $this->collectionFactory = $collectionFactory;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param AbstractCollection $collection
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\App\ResponseInterface
     * @throws \Exception
     */
    public function massAction(AbstractCollection $collection)
    {
        try {
            $pdfContent = $this->pdfShipment->getPdf($collection)->render();
            /** @var \Magento\Sales\Model\Order $order */
            foreach ($collection as $order) {
                $order->setStatus(\Magecom\BorrowHome\Model\OrderStatusInterface::ORDER_STATUS_ON_LOAN);
                $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING);
                $this->orderRepository->save($order);
            }
            $fileName = sprintf('delivery_note_%s.pdf', $this->dateTime->date('Y-m-d_H-i-s'));
            return $this->fileFactory->create($fileName, $pdfContent, DirectoryList::TMP, 'application/pdf');
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, $e->getMessage());
            $result = $this->resultRedirectFactory->create();
            $result->setPath('sales/order');
            return $result;
        }
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magecom_OrderPreparation::delivery_notes');
    }

}
