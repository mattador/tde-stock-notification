<?php

/**
 * @author Matthew Cooper
 * @author Matthew Cooper <matthew.cooper@thedailyedited.com>
 */
namespace Tde\StockNotifier\Block\Adminhtml\Catalog\Product\Edit;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Tde\GeneralPlugins\Helper\Stock;

/**
 * Class StockNotifier
 * @package Tde\StockNotifier\Block\Adminhtml\Catalog\Product
 */
class StockNotifier extends \Magento\Backend\Block\Template
{

    /**
     * @var Registry
     */
    protected $_registry;

    /**
     * @var mixed
     */
    protected $_product;

    /**
     * @var Stock
     */
    protected $_helper;

    /**
     * StockNotifier constructor.
     * @param Context $context
     * @param Registry $registry
     * @param Stock $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Stock $helper,
        array $data
    )
    {
        $this->_helper = $helper;
        $this->_product = $registry->registry('current_product');
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        //Only display wait list if 1. the product is a simple and 2. there is stock
        if (is_null($this->_product->getId()) || $this->_product->getTypeId() != 'simple' || $this->_helper->getProductStockStatus($this->_product)) {
            $config = $this->getData('config');
            $config['canShow'] = false;
            $this->setData('config', $config);
        }
        return parent::_prepareLayout();
    }

    /**
     * @return mixed
     */
    public function getProductId()
    {
        return $this->_product->getId();
    }

}
