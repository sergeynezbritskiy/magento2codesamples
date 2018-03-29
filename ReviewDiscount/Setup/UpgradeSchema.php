<?php

namespace Magecom\ReviewDiscount\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Class UpgradeSchema
 *
 * @package Magecom\ReviewDiscount\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * Upgrades DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '0.0.3') < 0) {
            $this->alterIntegerFormat($setup);
        }
        if (version_compare($context->getVersion(), '0.0.5') < 0) {
            $this->addCustomerDiscountRelation($setup);
        }
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     */
    private function addCustomerDiscountRelation(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable('magecom_customer_salesrule_coupon_relation'))
            ->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )
            ->addColumn(
                'customer_entity_id',
                Table::TYPE_INTEGER,
                10,
                ['unsigned' => true, 'nullable' => false],
                'Customer ID'
            )
            ->addColumn(
                'coupon_code',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Coupon ID'
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Creation Time'
            )
            ->addColumn(
                'updated_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                'Update Time'
            )
            ->addForeignKey(
                $setup->getFkName(
                    'magecom_customer_salesrule_coupon_relation',
                    'customer_entity_id',
                    'customer_entity',
                    'entity_id'
                ),
                'customer_entity_id',
                $setup->getTable('customer_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $setup->getFkName(
                    'magecom_customer_salesrule_coupon_relation',
                    'coupon_code',
                    'salesrule_coupon',
                    'code'
                ),
                'coupon_code',
                $setup->getTable('salesrule_coupon'),
                'code',
                Table::ACTION_CASCADE
            );
        $setup->getConnection()->createTable($table);
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function alterIntegerFormat(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable('magecom_review_discount_rule_entity');
        $setup->getConnection()->changeColumn($tableName, 'discount', 'discount', [
            'type' => Table::TYPE_DECIMAL,
            'nullable' => false,
            'length' => '4,2',
            'comment' => 'Discount Size'
        ]);
    }
}