<?php

namespace Magecom\BorrowHome\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magecom\BorrowHome\Model\OrderStatusInterface;

/**
 * Class OverrideRendererForBorrowHome
 * Observer for order_preparation_renderer_factory event
 *
 * @see \Magecom\OrderPreparation\Model\Order\Pdf\RendererFactory::getRenderer
 *
 * @package Magecom\BorrowHome\Observer
 */
class OverrideRendererForBorrowHome implements ObserverInterface, OrderStatusInterface
{

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Framework\DataObject $classWrapper */
        $classWrapper = $observer->getEvent()->getData('classWrapper');
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getData('order');
        $borrowHomeOrders = [
            self::ORDER_STATUS_BORROW_HOME,
            self::ORDER_STATUS_ON_LOAN,
            self::ORDER_STATUS_BORROW_HOME_RETURNED
        ];
        if (in_array($order->getStatus(), $borrowHomeOrders)) {
            $classWrapper->setData('class', \Magecom\BorrowHome\Model\Order\Pdf\BorrowHomeRenderer::class);
        }
    }

}