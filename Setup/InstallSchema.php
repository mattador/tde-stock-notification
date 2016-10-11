<?php
/**
 * @author Matthew Cooper
 * @author Matthew Cooper <matthew.cooper@thedailyedited.com>
 */
namespace Tde\StockNotifier\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        /**
         * Prepare database before module installation
         */
        $setup->startSetup();

        /**
         * Create table 'tde_stock_notifications'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('tde_stock_notifications')
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Stock Notification Id'
        )->addColumn(
            'email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            128,
            [],
            'Customer email'
        )->addColumn(
            'product_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'nullable' => false],
            'Product Id'
        )->addColumn(
            'sent',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            1,
            ['nullable' => false, 'default' => 0],
            'Has sent flag'
        )->addColumn(
            'sent_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => true],
            'Time back in notification email was sent'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Order Id'
        )->addIndex(
            $setup->getIdxName('tde_stock_notifications', ['sent']),
            ['sent']
        )->addIndex(
            $setup->getIdxName('tde_stock_notifications', ['product_id']),
            ['product_id']
        )->addIndex(
            $setup->getIdxName('tde_stock_notifications', ['created_at']),
            ['created_at']
        )->addForeignKey(
            $setup->getFkName('tde_stock_notifications', 'product_id', 'catalog_product_entity', 'entity_id'),
            'product_id',
            $setup->getTable('catalog_product_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_NO_ACTION
        )->setComment(
            'Tde custom back in stock notifications'
        );
        $setup->getConnection()->createTable($table);
        $setup->endSetup();
    }
}
