<?php

/**
 * @author Matthew Cooper
 * @author Matthew Cooper <matthew.cooper@thedailyedited.com>
 */
namespace Tde\StockNotifier\Model\ResourceModel\StockNotifier;

use Magento\Sales\Model\ResourceModel\Collection\AbstractCollection;

/**
 * StockNotifier entity collection
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Collection extends AbstractCollection
{
    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Tde\StockNotifier\Model\StockNotifier', 'Tde\StockNotifier\Model\ResourceModel\StockNotifier');
    }
}
