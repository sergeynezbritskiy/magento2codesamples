<?php

namespace Magecom\LandingPage\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 *
 * @package Magecom\Cms\Setup
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
        $setup->getConnection()->addColumn(
            $setup->getTable('cms_page'),
            'category_id',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'length' => 10,
                'nullable' => true,
                'unsigned' => true,
                'comment' => 'Catalog Category Entity Id'
            ]

        );
        $setup->getConnection()->addColumn(
            $setup->getTable('cms_page'),
            'category_url',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Catalog Original Url'
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('cms_page'),
            'category_query',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Formatted Query'
            ]
        );

        $setup->getConnection()->addForeignKey(
            $setup->getFkName('cms_page', 'category_id', 'catalog_category_entity', 'entity_id'),
            $setup->getTable('cms_page'),
            'category_id',
            $setup->getTable('catalog_category_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL
        );

    }
}