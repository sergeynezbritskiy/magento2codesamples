<?php

namespace Magecom\ReviewDiscount\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magecom\ReviewDiscount\Model\DiscountRule;

/**
 * Class UpgradeData
 *
 * @package Magecom\ReviewDiscount\Setup
 */
class UpgradeData implements UpgradeDataInterface
{

    /**
     * @var \Magecom\ReviewDiscount\Model\DiscountRuleFactory
     */
    private $discountRuleFactory;

    /**
     * @var \Magecom\ReviewDiscount\Model\ResourceModel\DiscountRule
     */
    private $discountRuleRepository;

    /**
     * PHP 5 allows developers to declare constructor methods for classes.
     * Classes which have a constructor method call this method on each newly-created object,
     * so it is suitable for any initialization that the object may need before it is used.
     *
     * Note: Parent constructors are not called implicitly if the child class defines a constructor.
     * In order to run a parent constructor, a call to parent::__construct() within the child constructor is required.
     *
     * param [ mixed $args [, $... ]]
     *
     * @link http://php.net/manual/en/language.oop5.decon.php
     * @param \Magecom\ReviewDiscount\Model\DiscountRuleFactory $discountRuleFactory
     * @param \Magecom\ReviewDiscount\Model\ResourceModel\DiscountRule $discountRuleRepository
     */
    public function __construct(
        \Magecom\ReviewDiscount\Model\DiscountRuleFactory $discountRuleFactory,
        \Magecom\ReviewDiscount\Model\ResourceModel\DiscountRule $discountRuleRepository
    )
    {
        $this->discountRuleFactory = $discountRuleFactory;
        $this->discountRuleRepository = $discountRuleRepository;
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
        $setup->startSetup();
        if (version_compare($context->getVersion(), '0.0.2') < 0) {
            $this->alterRulesForSocialNetworks($setup);
        }
        if (version_compare($context->getVersion(), '0.0.4') < 0) {
            $this->recreateRules($setup);
        }
        $setup->endSetup();
    }

    /**
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @throws \Exception
     */
    private function alterRulesForSocialNetworks(ModuleDataSetupInterface $setup)
    {
        /** @var DiscountRule $rule */
        $rule = $this->discountRuleFactory->create(['setup' => $setup]);
        $this->discountRuleRepository->load($rule, 'facebook_social_network', 'input_field');
        if ($rule->getEntityId()) {
            $rule->setInputField('facebook_shared');
            $this->discountRuleRepository->save($rule);
        }

        $rule = $this->discountRuleFactory->create(['setup' => $setup]);
        $this->discountRuleRepository->load($rule, 'google_plus_social_network', 'input_field');
        if ($rule->getEntityId()) {
            $rule->setInputField('google_plus_shared');
            $this->discountRuleRepository->save($rule);
        }
    }

    /**
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @throws \Exception
     */
    private function recreateRules(ModuleDataSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $table = $connection->getTableName('magecom_review_discount_rule_entity');
        $connection->truncateTable($table);
        $setup->getConnection()->insertArray($table,
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
                'discount' => 2.5,
                'description' => 'Nickname gives you 2.5% discount',
                'validator' => '\Magecom\ReviewDiscount\Model\DiscountRule\EmptyValidator',
                'validator_arguments' => null,
                'depends' => null,
                'dependency_condition' => null,
            ],
            [
                'entity_id' => 2,
                'enabled' => true,
                'input_field' => 'title',
                'discount' => 2.5,
                'description' => 'Title gives you 2.5% discount',
                'validator' => '\Magecom\ReviewDiscount\Model\DiscountRule\EmptyValidator',
                'validator_arguments' => null,
                'depends' => null,
                'dependency_condition' => null,
            ],
            [
                'entity_id' => 3,
                'enabled' => true,
                'input_field' => 'detail',
                'discount' => 2.5,
                'description' => 'Detail gives you 2.5% discount',
                'validator' => '\Magecom\ReviewDiscount\Model\DiscountRule\EmptyValidator',
                'validator_arguments' => null,
                'depends' => null,
                'dependency_condition' => null,
            ],
            [
                'entity_id' => 4,
                'enabled' => true,
                'input_field' => 'rating',
                'discount' => 2.5,
                'description' => 'Rating gives you 2.5% discount',
                'validator' => '\Magecom\ReviewDiscount\Model\DiscountRule\EmptyValidator',
                'validator_arguments' => null,
                'depends' => null,
                'dependency_condition' => null,
            ],
            [
                'entity_id' => 6,
                'enabled' => true,
                'input_field' => 'file',
                'discount' => 5,
                'description' => 'File should be uploaded, also all dependable fields should be applied, if yes, then give 5% more discount',
                'validator' => '\Magecom\ReviewDiscount\Model\DiscountRule\EmptyValidator',
                'validator_arguments' => null,
                'depends' => '1,2,3,4',
                'dependency_condition' => null,//all rules should be applied
            ],
        ];
    }

}