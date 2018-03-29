<?php

namespace Magecom\LandingPage\Model\Page\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Categories
 * @package Magecom\LandingPage\Model\Page\Source
 */
class Categories implements OptionSourceInterface
{

    /**
     * @var \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface
     */
    protected $pageLayoutBuilder;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * Pages constructor.
     *
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collectionFactory
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collectionFactory,
        \Magento\Framework\App\Request\Http $request
    )
    {
        $this->collectionFactory = $collectionFactory;
        $this->request = $request;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $collection */
            $collection = $this->collectionFactory->create();
            $collection->addFieldToSelect('name');
            $options = [];
            $options[] = [
                'label' => '-- Select Category --',
                'value' => '0',
            ];
            /** @var \Magento\Catalog\Model\Category $category */
            foreach ($collection as $category) {
                $options[] = [
                    'label' => sprintf('%s (ID: %d)', $category->getName(), $category->getEntityId()),
                    'value' => $category->getEntityId(),
                ];
            }
            $this->options = $options;

        }
        return $this->options;
    }

}
