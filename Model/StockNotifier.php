<?php

/**
 * @author Matthew Cooper
 * @author Matthew Cooper <matthew.cooper@thedailyedited.com>
 */
namespace Tde\StockNotifier\Model;

use Magento\Framework\Model\AbstractModel;

class StockNotifier extends AbstractModel
{

    protected function _construct()
    {
        $this->_init('Tde\StockNotifier\Model\ResourceModel\StockNotifier');
    }
}