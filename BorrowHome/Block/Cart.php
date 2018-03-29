<?php

namespace Magecom\BorrowHome\Block;

/**
 * Class Cart
 * @package Magecom\BorrowHome\Block
 */
class Cart extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magecom\BorrowHome\Model\Session
     */
    private $session;

    /**
     * Cart constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magecom\BorrowHome\Model\Session $session
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magecom\BorrowHome\Model\Session $session,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->session = $session;
    }

    /**
     * @return array
     */
    public function getCartItemsIds()
    {
        return $this->session->getProducts();
    }

    /**
     * @param int $productId
     * @return string
     */
    public function getDeleteItemUrl($productId)
    {
        return $this->getUrl('borrow-home/product/delete', ['product_id' => $productId]);
    }


    /**
     * @param array $cartItemData
     * @return string
     */
    public function getCartItemHtml($cartItemData = null)
    {
        /** @var \Magecom\BorrowHome\Block\Cart\Item $block */
        $block = $this->getLayout()->createBlock(\Magecom\BorrowHome\Block\Cart\Item::class);
        $block->setCartItemData($cartItemData);
        $block->setTemplate('cart/item.phtml');
        return $block->toHtml();
    }

    /**
     * @return string
     */
    public function getEmptyCartItemHtml()
    {
        /** @var \Magecom\BorrowHome\Block\Cart\Item $block */
        $block = $this->getLayout()->createBlock(\Magecom\BorrowHome\Block\Cart\EmptyItem::class);
        $block->setTemplate('cart/item/empty.phtml');
        return $block->toHtml();
    }
}