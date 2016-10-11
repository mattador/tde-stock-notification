<?php

/**
 * @author Matthew Cooper
 * @author Matthew Cooper <matthew.cooper@thedailyedited.com>
 */
namespace Tde\StockNotifier\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class StockNotifier extends AbstractDb
{

    protected function _construct()
    {
        $this->_init('tde_stock_notifications', 'entity_id');
    }
}