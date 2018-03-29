<?php

namespace Magecom\ReviewDiscount\Helper;

use Magento\SalesRule\Model\Rule;

/**
 * Class RulesHelper
 *
 * @package Magecom\ReviewDiscount\Helper
 */
class RulesHelper extends \Magento\Framework\App\Helper\AbstractHelper
{

    const DISCOUNT_RULE_NAME_SFX = '% Review Discount';

    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    private $ruleFactory;

    /**
     * @var \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Magento\SalesRule\Model\ResourceModel\Rule
     */
    private $ruleRepository;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Group\Collection
     */
    private $customerGroupCollection;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManagerInterface;

    /**
     * RulesHelper constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\SalesRule\Model\RuleFactory $ruleFactory
     * @param \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $collectionFactory
     * @param \Magento\SalesRule\Model\ResourceModel\Rule $ruleRepository
     * @param \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroupCollection
     * @param \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\SalesRule\Model\RuleFactory $ruleFactory,
        \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $collectionFactory,
        \Magento\SalesRule\Model\ResourceModel\Rule $ruleRepository,
        \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroupCollection,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
    )
    {
        parent::__construct($context);
        $this->ruleFactory = $ruleFactory;
        $this->collectionFactory = $collectionFactory;
        $this->ruleRepository = $ruleRepository;
        $this->customerGroupCollection = $customerGroupCollection;
        $this->storeManagerInterface = $storeManagerInterface;
    }

    /**
     * @param int $discount
     * @return Rule
     */
    public function ensureRuleWithDiscount($discount)
    {
        $ruleName = $discount . self::DISCOUNT_RULE_NAME_SFX;
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('name', $ruleName);
        foreach ($collection as $item) {
            return $item;
        }
        return $this->createRule($discount);
    }

    /**
     * @param int $discount
     * @return  Rule $rule
     */
    private function createRule($discount)
    {
        $ruleName = $discount . self::DISCOUNT_RULE_NAME_SFX;
        $rule = $this->ruleFactory->create();
        $rule->setName($ruleName);
        $rule->setIsActive(true);
        $rule->setDescription(sprintf('%d%% discount per review', $discount));
        $rule->setStopRulesProcessing(false);
        $rule->setSimpleAction(\Magento\SalesRule\Model\Rule::BY_PERCENT_ACTION);
        $rule->setDiscountAmount($discount);
        $rule->setCouponType(\Magento\SalesRule\Model\Rule::COUPON_TYPE_SPECIFIC);
        $rule->setUsesPerCoupon(1);
        $rule->setUseAutoGeneration(true);
        $rule->setWebsiteIds($this->getWebsiteIds());
        $rule->setCustomerGroupIds($this->getCustomerGroupsIds());
        $this->ruleRepository->save($rule);
        return $rule;
    }

    /**
     * @return string
     */
    private function getWebsiteIds()
    {
        $websites = $this->storeManagerInterface->getWebsites();
        $result = [];
        foreach ($websites as $website) {
            $result[] = $website->getId();
        }
        return implode(',', $result);
    }

    /**
     * @return string
     */
    private function getCustomerGroupsIds()
    {
        $customerGroups = $this->customerGroupCollection->toArray();
        array_walk($customerGroups['items'], function (&$item) {
            $item = $item['customer_group_id'];
        });
        return implode(',', $customerGroups['items']);
    }

}