<?php

namespace Magecom\BorrowHome\Observer;


use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magecom\BorrowHome\Model\OrderStatusInterface;

/**
 * Class UpdateMassActionsList
 * Observer for order_preparation_massaction_packaging_norway event
 * @see \Magecom\OrderPreparation\Ui\Component\Massaction\OrderStatus\AbstractChangeStatus::jsonSerialize
 *
 * @package Magecom\BorrowHome\Observer
 */
class UpdateMassActionsList implements ObserverInterface, OrderStatusInterface
{

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Framework\DataObject $actions */
        $actions = $observer->getEvent()->getData('actions');
        /** @var \Magento\Sales\Model\Order\Status[] $statuses */
        $statuses = $observer->getEvent()->getData('statuses');

        /** @var \Magento\Framework\UrlInterface $urlBuilder */
        $urlBuilder = $observer->getEvent()->getData('url_builder');
        $actionsArray = $actions->getData();
        $statusesAvailable = [
            self::ORDER_STATUS_BORROW_HOME,
            self::ORDER_STATUS_ON_LOAN,
            self::ORDER_STATUS_BORROW_HOME_RETURNED,
        ];
        $borrowHomeStatuses = [];
        $order = 15;
        foreach ($statusesAvailable as $statusKey) {
            $order++;
            $status = $statuses[$statusKey];
            $borrowHomeStatuses[] = [
                'order' => $order * 10,
                'status' => $status->getStatus(),
                'label' => $status->getLabel()
            ];
        }
        $borrowHomeStatuses[] = [
            'order' => 149, //right before on loan,
            'status' => self::ORDER_STATUS_ON_LOAN,
            'label' => __('Print Home Try Labels'),
            'url' => $urlBuilder->getUrl('borrow_home/pdf/deliverynotes'),
            'type' => 'borrow_home_pdf_delivery_notes',
        ];
        $actions->setData(array_merge($actionsArray, $borrowHomeStatuses));
    }

}