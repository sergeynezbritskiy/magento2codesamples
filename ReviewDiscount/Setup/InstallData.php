<?php

namespace Magecom\ReviewDiscount\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magecom\ReviewDiscount\Model\DiscountRule;

/**
 * Class InstallData
 *
 * @package Magecom\ReviewDiscount\Setup
 */
class InstallData implements InstallDataInterface
{

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

        $this->createDefaultRules($setup);

        /**
         * Prepare database after install
         */
        $setup->endSetup();
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    private function createDefaultRules(ModuleDataSetupInterface $setup)
    {
        $setup->getConnection()->insertArray(
            $setup->getTable(DiscountRule::ENTITY . '_entity'),
            ['entity_id', 'enabled', 'input_field', 'discount', 'description', 'validator', 'validator_arguments', 'depends', 'dependency_condition'],
            $this->getRulesData()
        );
    }

    /**
     * @return array
     */
    private function getRulesData()
    {
        return [
            [
                'entity_id' => 1,
                'enabled' => true,
                'input_field' => 'nickname',
                'discount' => 0,
                'description' => 'Nickname should not be empty, but this would be not enough for discount so 0% discount for this rule',
                'validator' => '\Magecom\ReviewDiscount\Model\DiscountRule\EmptyValidator',
                'validator_arguments' => null,
                'depends' => null,
                'dependency_condition' => null,
            ],
            [
                'entity_id' => 2,
                'enabled' => true,
                'input_field' => 'title',
                'discount' => 0,
                'description' => 'Title should not be empty, but this would be not enough for discount so 0% discount for this rule',
                'validator' => '\Magecom\ReviewDiscount\Model\DiscountRule\EmptyValidator',
                'validator_arguments' => null,
                'depends' => null,
                'dependency_condition' => null,
            ],
            [
                'entity_id' => 3,
                'enabled' => true,
                'input_field' => 'detail',
                'discount' => 0,
                'description' => 'Detail should not be empty, but this would be not enough for discount so 0% discount for this rule',
                'validator' => '\Magecom\ReviewDiscount\Model\DiscountRule\EmptyValidator',
                'validator_arguments' => null,
                'depends' => null,
                'dependency_condition' => null,
            ],
            [
                'entity_id' => 4,
                'enabled' => true,
                'input_field' => 'rating',
                'discount' => 0,
                'description' => 'Rating should not be empty, but this would be not enough for discount so 0% discount for this rule',
                'validator' => '\Magecom\ReviewDiscount\Model\DiscountRule\EmptyValidator',
                'validator_arguments' => null,
                'depends' => null,
                'dependency_condition' => null,
            ],
            [
                'entity_id' => 5,
                'enabled' => true,
                'input_field' => null,
                'discount' => 5,
                'description' => 'Aggregation rule, all dependable fields should be applied, if yes, then give 5% discount',
                'validator' => null,
                'validator_arguments' => null,
                'depends' => '1,2,3,4',
                'dependency_condition' => null,//all rules should be applied
            ],
            [
                'entity_id' => 6,
                'enabled' => true,
                'input_field' => 'file',
                'discount' => 5,
                'description' => 'File should be uploaded, also all dependable fields should be applied, if yes, then give 5% more discount',
                'validator' => '\Magecom\ReviewDiscount\Model\DiscountRule\EmptyValidator',
                'validator_arguments' => null,
                'depends' => '5',
                'dependency_condition' => null,//all rules should be applied
            ],
            [
                'entity_id' => 7,
                'enabled' => true,
                'input_field' => 'facebook_social_network',
                'discount' => 0,
                'description' => 'Product should be shared to Facebook, also all dependable rules should be applied',
                'validator' => '\Magecom\ReviewDiscount\Model\DiscountRule\EmptyValidator',
                'validator_arguments' => null,
                'depends' => '6',
                'dependency_condition' => null,//all rules should be applied
            ],
            [
                'entity_id' => 8,
                'enabled' => true,
                'input_field' => 'google_plus_social_network',
                'discount' => 0,
                'description' => 'Product should be shared to Google+, also all dependable rules should be applied',
                'validator' => '\Magecom\ReviewDiscount\Model\DiscountRule\EmptyValidator',
                'validator_arguments' => null,
                'depends' => '6',
                'dependency_condition' => null,//all rules should be applied
            ],
            [
                'entity_id' => 9,
                'enabled' => true,
                'input_field' => null,
                'discount' => 5,
                'description' => 'Aggregation rule, at least one rule should be applied, if yes, then give 5%',
                'validator' => null,
                'validator_arguments' => null,
                'depends' => '7,8',
                'dependency_condition' => 1,//at least one rule should be applied
            ],
        ];
    }

}