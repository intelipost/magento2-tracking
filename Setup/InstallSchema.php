<?php
/*
 * @package     Intelipost_Tracking
 * @copyright   Copyright (c) 2017 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author      Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

namespace Intelipost\Tracking\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
 
class InstallSchema implements InstallSchemaInterface
{

public function install (SchemaSetupInterface $setup, ModuleContextInterface $context)
{
    $installer = $setup;

    $installer->startSetup ();

    /*
     * Sales Shipment Track
     */
    $result = $installer->getConnection()
        ->addColumn(
            $installer->getTable('sales_shipment_track'),
            'track_url',
            array(
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Intelipost Tracking URL',
                'after' => 'track_number'
            )
        );

    $installer->endSetup();
}

}

