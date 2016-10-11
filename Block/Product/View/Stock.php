<?php

/**
 * @author Matthew Cooper
 * @author Matthew Cooper <matthew.cooper@thedailyedited.com>
 */
namespace Tde\StockNotifier\Block\Product\View;

use Magento\Customer\Model\Session;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Magento\ProductAlert\Helper\Data;
use Tde\GeneralPlugins\Helper\Stock as StockHelper;

class Stock extends \Magento\ProductAlert\Block\Product\View
{

    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var Registry
     */
    protected $_registry;

    /**
     * @var StockHelper
     */
    protected $_stockHelper;

    /**
     * @param Context $context
     * @param Data $helper
     * @param Registry $registry
     * @param PostHelper $coreHelper
     * @param Session $_customerSession
     * @param StockHelper $stockHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $helper,
        Registry $registry,
        PostHelper $coreHelper,
        Session $_customerSession,
        StockHelper $stockHelper,
        array $data
    )
    {
        $this->_registry = $registry;
        $this->_customerSession = $_customerSession;
        $this->_stockHelper = $stockHelper;
        parent::__construct($context, $helper, $registry, $coreHelper, $data);
    }

    /**
     * Prepare stock info
     *
     * @param string $template
     * @return $this
     */
    public function setTemplate($template)
    {

        if (!$this->_helper->isStockAlertAllowed() || !$this->getProduct() ||
            //Check if stock is available - careful of the inverse condition - the template is blanked out if there is stock
            $this->_stockHelper->getProductStockStatus($this->getProduct())
            /*$this->getProduct()->isAvailable()*/
        ) {
            $template = '';
        }
        return parent::setTemplate($template);
    }

    /**
     * Returns customer email if logged in
     * @return string
     */
    public function getCustomerEmail()
    {
        if (!$this->_customerSession->isLoggedIn()) {
            return '';
        }
        return $this->_customerSession->getCustomer()->getEmail();
    }

    /**
     * Retrieve currently edited product object
     *
     * @return \Magento\Catalog\Model\Product|boolean
     */
    public function getProduct()
    {
        $product = $this->_registry->registry('current_product');
        if ($product && $product->getId()) {
            return $product;
        }
        return false;
    }

}