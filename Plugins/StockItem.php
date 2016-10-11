<?php

/**
 * @author Matthew Cooper
 * @author Matthew Cooper <matthew.cooper@thedailyedited.com>
 */
namespace Tde\StockNotifier\Plugins;

use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Tde\StockNotifier\Model\Email;
use Tde\StockNotifier\Model\ResourceModel\StockNotifier;
use Tde\StockNotifier\Model\StockNotifierFactory;

class StockItem
{

    /**
     * @var StockNotifierFactory
     */
    protected $_stockNotifierFactory;

    /**
     * @var Email
     */
    protected $_email;

    /**
     * Date
     *
     * @var DateTime
     */
    protected $_date;

    /**
     * StockItem constructor.
     * @param StockNotifierFactory $stockNotifierFactory
     * @param Email $email
     * @param DateTime $date
     */
    public function __construct(
        StockNotifierFactory $stockNotifierFactory,
        Email $email,
        DateTime $date
    )
    {
        $this->_email = $email;
        $this->_stockNotifierFactory = $stockNotifierFactory;
        $this->_date = $date;
    }

    /**
     * @param StockItemRepository $subject
     * @param StockItemInterface $stockItem
     * @return StockItemInterface
     */
    public function afterSave(StockItemRepository $subject, StockItemInterface $stockItem)
    {
        if ($stockItem->getTypeId() == 'simple' && $stockItem->getIsInStock() && $stockItem->getQty() >= 5) {
            $stockNotifier = $this->_stockNotifierFactory->create();
            /** @var StockNotifier $collection */
            $collection = $stockNotifier->getCollection();
            $collection->addFieldToFilter('product_id', $stockItem->getProductId())
                ->addFieldToFilter('sent', 0);
            //client already subscribed
            if ($collection->count() > 0) {
                /** @var \Tde\StockNotifier\Model\StockNotifier $notification */
                $sent = []; //prevent accidental duplication - @todo correct with SQL unique key across email and product_id field
                foreach ($collection->getItems() as $notification) {
                    if (!in_array($notification->getEmail(), $sent)) {
                        //dispatch back in stock email
                        $this->_email->send($notification->getEmail(), $stockItem->getProductId());
                    }
                    $notification
                        ->setData('sent', 1)
                        ->setData('sent_at', $this->_date->gmtDate())
                        ->save();
                    $sent[] = $notification->getEmail();
                }
            }
        }
        return $stockItem;
    }

}
