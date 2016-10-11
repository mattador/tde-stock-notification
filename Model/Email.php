<?php

/**
 * @author Matthew Cooper
 * @author Matthew Cooper <matthew.cooper@thedailyedited.com>
 */
namespace Tde\StockNotifier\Model;

use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Model\Context;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Tde\StockNotifier\Helper\StockNotifier;

class Email
{

    const XML_PATH_EMAIL_IDENTITY = 'catalog/productalert/email_identity';

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var StockNotifier
     */
    protected $_stockNotifierHelper;

    /**
     * @var Emulation
     */
    protected $_appEmulation;

    /**
     * @var TransportBuilder
     */
    protected $_transportBuilder;


    /**
     * @var \Magento\Framework\App\State
     */
    protected $_appState;


    /**
     * Core store config
     *
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Email constructor.
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param Emulation $appEmulation
     * @param TransportBuilder $transportBuilder
     * @param StockNotifier $stockNotifierHelper
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        Emulation $appEmulation,
        TransportBuilder $transportBuilder,
        StockNotifier $stockNotifierHelper
    )
    {
        $this->_stockNotifierHelper = $stockNotifierHelper;
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_appEmulation = $appEmulation;
        $this->_transportBuilder = $transportBuilder;
        $this->_appState = $context->getAppState();
    }

    /**
     * @param $email
     * @param $productId
     * @return bool
     */
    public function send($email, $productId)
    {
        $storeId = Store::DISTRO_STORE_ID;
        $this->_appEmulation->startEnvironmentEmulation(1);
        $block = $this->_stockNotifierHelper->createBlock('Tde\StockNotifier\Block\Email\Stock');
        $block->addProduct($this->_stockNotifierHelper->getTargetProduct($productId));
        $productHtml = $this->_appState->emulateAreaCode(
            Area::AREA_FRONTEND,
            [$block, 'toHtml']
        );
        $this->_appEmulation->stopEnvironmentEmulation();
        $to = array($email, '');
        $transport = $this->_transportBuilder->setTemplateIdentifier('stock_notifier_alert')
            ->setTemplateOptions(array('area' => Area::AREA_FRONTEND, 'store' => $storeId))
            ->setTemplateVars(array(
                'product' => $productHtml
            ))
            ->setFrom($this->_scopeConfig->getValue(
                self::XML_PATH_EMAIL_IDENTITY,
                ScopeInterface::SCOPE_STORE,
                $storeId
            ))
            ->addTo($to)
            ->getTransport();
        $transport->sendMessage();
        return true;
    }
}
