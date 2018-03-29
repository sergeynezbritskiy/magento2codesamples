<?php

namespace Magecom\BorrowHome\Setup;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magecom\Extraoptical\Model\AttributeSetInterface;

/**
 * Class UpgradeData
 * @package Magecom\BorrowHome\Setup
 */
class UpgradeData implements UpgradeDataInterface, AttributeSetInterface
{

    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var \Magento\Catalog\Setup\CategorySetupFactory
     */
    private $categorySetupFactory;

    /**
     * PHP 5 allows developers to declare constructor methods for classes.
     * Classes which have a constructor method call this method on each newly-created object,
     * so it is suitable for any initialization that the object may need before it is used.
     *
     * Note: Parent constructors are not called implicitly if the child class defines a constructor.
     * In order to run a parent constructor, a call to parent::__construct() within the child constructor is required.
     *
     * param [ mixed $args [, $... ]]
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     * @param \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory
     * @link http://php.net/manual/en/language.oop5.decon.php
     */
    public function __construct(
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
        \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory

    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->categorySetupFactory = $categorySetupFactory;
    }

    /**
     * Upgrades data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /**
         * Prepare database before install
         */
        $setup->startSetup();

        if (version_compare($context->getVersion(), '0.0.2') < 0) {
            $this->createApplicableForBorrowHomeAttribute($setup);
        }

        /**
         * Prepare database after install
         */
        $setup->endSetup();
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    private function createApplicableForBorrowHomeAttribute(ModuleDataSetupInterface $setup)
    {

        $attributeCode = 'borrow_home';
        $attributeLabel = 'Borrow Home';

        /** @var \Magento\Catalog\Setup\CategorySetup $categorySetup */
        $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
        /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            $attributeCode,
            [
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => $attributeLabel,
                'input' => 'select',
                'class' => '',
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Table::class,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => true,
                'visible_on_front' => true,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => '',
                'sort_order' => 101,
                'option' => [
                    'order' => [
                        'available' => 1,
                        'not_available' => 2,
                    ],
                    'value' => [
                        'available' => [
                            '0' => 'Available',//admin label
                            '1' => 'Available',//store label
                        ],
                        'not_available' => [
                            '0' => 'Not Available',//admin label
                            '1' => 'Not Available',//store label
                        ],
                    ]
                ],
            ]
        );
        $eavSetup->addAttributeToSet($entityTypeId, self::ATTRIBUTE_SET_EYEGLASSES, 'General', $attributeCode, 90);
    }

}