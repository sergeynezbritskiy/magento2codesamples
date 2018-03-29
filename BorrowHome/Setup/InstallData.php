<?php

namespace Magecom\BorrowHome\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class InstallData
 * @package Magecom\BorrowHome\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * InstallData constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Installs data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /**
         * Prepare database before install
         */
        $setup->startSetup();

        $this->createCustomStatuses($setup);

        /**
         * Prepare database after install
         */
        $setup->endSetup();
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    private function createCustomStatuses(ModuleDataSetupInterface $setup)
    {
        /** @var \Magento\Sales\Model\ResourceModel\Order\Status $statusRepository */
        $statusRepository = $this->objectManager->get(\Magento\Sales\Model\ResourceModel\Order\Status::class);

        foreach ($this->getCustomStatuses() as $state => $statusData) {
            /** @var \Magento\Sales\Model\Order\Status $status */
            $status = $this->objectManager->create(\Magento\Sales\Model\Order\Status::class, ['setup' => $setup]);

            $status->setData($statusData);
            $statusRepository->save($status);
            $status->assignState($state, false);
        }
    }

    /**
     * @return array
     */
    private function getCustomStatuses()
    {
        return [
            //china orders
            \Magento\Sales\Model\Order::STATE_NEW => [
                'status' => 'borrow_home',
                'label' => 'Borrow Home',
            ],
            \Magento\Sales\Model\Order::STATE_PROCESSING =>
                [
                    'status' => 'on_loan',
                    'label' => 'On Loan'
                ],
            \Magento\Sales\Model\Order::STATE_CANCELED =>
                [
                    'status' => 'borrow_home_returned',
                    'label' => 'Borrow Home Returned'
                ],
        ];
    }

}