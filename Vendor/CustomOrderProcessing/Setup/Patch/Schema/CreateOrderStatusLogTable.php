<?php

declare(strict_types=1);

namespace Vendor\CustomOrderProcessing\Setup\Patch\Schema;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Zend_Db_Exception;

class CreateOrderStatusLogTable implements SchemaPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(ModuleDataSetupInterface $moduleDataSetup)
    {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * @return void
     * @throws Zend_Db_Exception
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        if (!$this->moduleDataSetup->tableExists('vendor_order_status_log')) {
            $table = $this->moduleDataSetup->getConnection()->newTable(
                $this->moduleDataSetup->getTable('vendor_order_status_log')
            )->addColumn(
                'log_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                'Log ID'
            )->addColumn(
                'order_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Order ID'
            )->addColumn(
                'old_status',
                Table::TYPE_TEXT,
                64,
                ['nullable' => false],
                'Old Status'
            )->addColumn(
                'new_status',
                Table::TYPE_TEXT,
                64,
                ['nullable' => false],
                'New Status'
            )->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Created At'
            )->setComment('Order Status Change Log Table');

            $this->moduleDataSetup->getConnection()->createTable($table);
        }

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @return array
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @return array
     */
    public function getAliases()
    {
        return [];
    }
}
