<?php

/**
 * @author Matthew Cooper
 * @author Matthew Cooper <matthew.cooper@thedailyedited.com>
 */
namespace Tde\StockNotifier\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Tde\StockNotifier\Helper\StockNotifier;
use Tde\StockNotifier\Helper\StockNotifier as StockNotifierHelper;
use Tde\StockNotifier\Model\StockNotifierFactory;

class Index extends Action
{

    /**
     * @var StockNotifier
     */
    protected $_stockNotifierHelper;

    /**
     * @var StockNotifierFactory
     */
    protected $_stockNotifierFactory;

    /**
     * @var JsonFactory
     */
    protected $_resultJson;

    /**
     * @param Context $context
     * @param StockNotifierFactory $stockNotifierFactory
     * @param StockNotifierHelper $stockNotifierHelper
     * @param JsonFactory $resultJson
     */
    public function __construct(
        Context $context,
        StockNotifierFactory $stockNotifierFactory,
        StockNotifierHelper $stockNotifierHelper,
        JsonFactory $resultJson
    )
    {
        $this->_stockNotifierHelper = $stockNotifierHelper;
        $this->_stockNotifierFactory = $stockNotifierFactory;
        $this->_resultJson = $resultJson->create();
        return parent::__construct($context);
    }

    /**
     * @return $this
     */
    public function execute()
    {
        // /subscribe-stock/index/index/email/matthew.cooper@thedailyedited.com/product/5722
        $params = $this->getRequest()->getParams();
        if (!isset($params['email']) || !\Zend_Validate::is($params['email'], 'EmailAddress')) {
            return $this->_resultJson->setData(
                [
                    'success' => false,
                    'msg' => __('Please enter a valid email address.')
                ]
            );
        }
        if (!isset($params['pid']) || !$this->_stockNotifierHelper->productExists($params['pid'])
        ) {
            return $this->_resultJson->setData(
                [
                    'success' => false,
                    'msg' => __('Sorry! Something went wrong.')
                ]
            );
        }

        if (isset($params['super'])) {
            //look for actual product id (if simple)
            if ($product = $this->_stockNotifierHelper->getProductId($params['pid'], $params['super'])) {
                $params['pid'] = $product->getId();
            }
        }

        /**
         * @var \Tde\StockNotifier\Model\ResourceModel\StockNotifier
         */
        $collection = $this->_stockNotifierFactory->create()->getCollection();
        $collection->addFieldToFilter('email', $params['email'])
            ->addFieldToFilter('product_id', $params['pid'])
            ->addFieldToFilter('sent', 0);

        //client already subscribed
        if ($collection->count() > 0) {
            return $this->_resultJson->setData(
                [
                    'success' => true,
                    'msg' => __('Thanks! You are already subscribed to this product.')
                ]
            );
        }

        /** @var \Tde\StockNotifier\Model\StockNotifier $notifier */
        $notifier = $this->_stockNotifierFactory->create();
        $notifier->setData([
            'email' => $params['email'],
            'product_id' => $params['pid']
        ])->save();

        return $this->_resultJson->setData(
            [
                'success' => true,
                'msg' => 'Thanks! We\'ll send you an email as soon as this product comes back in stock.'
            ]
        );
    }
}
