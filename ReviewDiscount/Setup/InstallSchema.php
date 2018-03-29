<?php

namespace Magecom\ReviewDiscount\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magecom\ReviewDiscount\Model\DiscountRule;

/**
 * Class InstallSchema
 * @package Magecom\ReviewDiscount\Setup
 */
class InstallSchema implements InstallSchemaInterface
{

    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->createDiscountRulesTable($setup);

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function createDiscountRulesTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(DiscountRule::ENTITY . '_entity'))
            ->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )
            ->addColumn(
                'enabled',
                Table::TYPE_INTEGER,
                4,
                ['unsigned' => true, 'nullable' => false],
                'Enabled\Disabled'
            )
            ->addColumn(
                'input_field',
                Table::TYPE_TEXT,
                128,
                [],
                'Input Filed the rule should be applied to'
            )
            ->addColumn(
                'discount',
                Table::TYPE_INTEGER,
                4,
                ['unsigned' => true, 'nullable' => false],
                'Discount Size'
            )
            ->addColumn(
                'description',
                Table::TYPE_TEXT,
                500,
                [],
                'Rule Description'
            )
            ->addColumn(
                'validator',
                Table::TYPE_TEXT,
                128,
                ['nullable' => true],
                'Validator name current rule should be applied with'
            )
            ->addColumn(
                'validator_arguments',
                Table::TYPE_TEXT,
                128,
                [],
                'Validator arguments if required, json encoded'
            )
            ->addColumn(
                'depends',
                Table::TYPE_TEXT,
                200,
                ['nullable' => true],
                'The list of rules IDs, current rule depends on'
            )
            ->addColumn(
                'dependency_condition',
                Table::TYPE_INTEGER,
                10,
                ['unsigned' => true, 'nullable' => true],
                'The number of rules to be applied, empty if all'
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
            );
        $setup->getConnection()->createTable($table);
    }

}