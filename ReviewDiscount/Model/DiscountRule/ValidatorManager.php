<?php

namespace Magecom\ReviewDiscount\Model\DiscountRule;

use Exception;

/**
 * Class ValidatorManager
 *
 * This class is actually a factory, but renamed to manager
 * in order to avoid conflicts with Magento 2 factories
 * @package Magecom\ReviewDiscount\Model\DiscountRule
 */
class ValidatorManager
{

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * ValidatorManager constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @return array
     */
    public function getValidatorsAvailable()
    {
        return [
            \Magecom\ReviewDiscount\Model\DiscountRule\EmptyValidator::class,
        ];
    }

    /**
     * @param $type
     * @param array $arguments
     * @return AbstractValidator
     * @throws Exception
     */
    public function create($type, array $arguments = [])
    {
        if (!in_array(ltrim($type, '\\'), $this->getValidatorsAvailable())) {
            throw new Exception('Validator ' . $type . 'not registered for ' . __CLASS__);
        }
        return $this->objectManager->create($type, $arguments);
    }


}