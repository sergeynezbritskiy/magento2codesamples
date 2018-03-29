<?php

namespace Magecom\ReviewDiscount\Model;

use Exception;
use Magecom\ReviewDiscount\Model\DiscountRule\AbstractValidator;
use Magecom\ReviewDiscount\Model\ResourceModel\DiscountRule\Collection;

/**
 * Class DiscountRule
 *
 * @package Magecom\ReviewDiscount\Model
 * @method integer getDiscount()
 * @method void setDiscount(integer $discount)
 * @method string getValidator()
 * @method void setValidator(string $validator)
 * @method string getInputField()
 * @method void setInputField(string $inputField)
 * @method integer getDependencyCondition()
 * @method void setDependencyCondition(integer $dependencyCondition)
 */
class DiscountRule extends \Magento\Framework\Model\AbstractModel
{

    const ENTITY = 'magecom_review_discount_rule';

    /**
     * @var bool
     */
    private $isApplicable;

    /**
     * @var bool
     */
    private $isInProgress;
    /**
     * @var DiscountRule\ValidatorManager
     */
    private $validatorManager;

    /**
     * DiscountRule constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param DiscountRule\ValidatorManager $validatorManager
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magecom\ReviewDiscount\Model\DiscountRule\ValidatorManager $validatorManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->validatorManager = $validatorManager;
    }

    /**
     * @param array $data
     * @param Collection $collection
     * @return bool
     * @throws Exception
     */
    public function isApplicable(array $data, Collection $collection)
    {
        //prevent cyclic loops
        if ($this->isInProgress) {
            throw new Exception('We have found cyclic dependencies with DiscountRule #' . $this->getId());
        }
        $this->isInProgress = true;


        if (is_null($this->isApplicable)) {
            /** @var AbstractValidator $validator */
            if ($this->getValidator()) {
                $validator = $this->validatorManager->create($this->getValidator(), $this->getValidatorArguments());
                $field = $this->getInputField();
                $fieldData = array_key_exists($field, $data) ? $data[$field] : null;
                $isApplicable = $validator->validate($fieldData);
            } else {
                $isApplicable = true;
            }
            $dependenciesApplicable = $this->calculateDependencies($data, $collection);
            $this->isApplicable = $isApplicable && $dependenciesApplicable;
        }

        $this->isInProgress = false;
        return $this->isApplicable;
    }

    /**
     * @return array
     */
    public function getValidatorArguments()
    {
        $result = $this->getData('validator_arguments');
        return empty($result) ? [] : json_encode($result, true);
    }

    /**
     * @param array|string $arguments
     */
    public function setValidatorArguments($arguments)
    {
        $this->setData('validator_arguments', is_array($arguments) ? json_encode($arguments) : $arguments);
    }

    /**
     * @return array
     */
    public function getDepends()
    {
        $dependsData = $this->getData('depends');
        return $dependsData ? explode(',', $this->getData('depends')) : [];
    }

    /**
     * @param array|string $depends
     * @return void
     */
    public function setDepends($depends)
    {
        $this->setData('depends', is_array($depends) ? implode(',', $depends) : $depends);
    }

    /**
     * Model construct that should be used for object initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Magecom\ReviewDiscount\Model\ResourceModel\DiscountRule::class);
    }

    /**
     * @param array $data
     * @param Collection $collection
     * @return bool
     * @throws Exception
     */
    private function calculateDependencies(array $data, Collection $collection)
    {
        $dependencies = $this->getDepends();
        $dependenciesApplicableCount = 0;
        $dependencyCondition = $this->getDependencyCondition() ?: count($dependencies);
        foreach ($dependencies as $dependencyId) {
            /** @var DiscountRule $dependency */
            $dependency = $collection->getItemById($dependencyId);
            if (is_null($dependency)) {
                throw new Exception('Unresolved dependency with ID#' . $dependencyId);
            }
            $dependenciesApplicableCount += (int)$dependency->isApplicable($data, $collection);
        }
        return $dependenciesApplicableCount >= $dependencyCondition;
    }

}