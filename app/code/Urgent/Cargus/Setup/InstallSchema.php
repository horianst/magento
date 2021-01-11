<?php

namespace Urgent\Cargus\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 *
 * @package Toptal\Blog\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * Install Blog Posts table
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $tableName = $setup->getTable('awb_expeditii');

        if ($setup->getConnection()->isTableExists($tableName) != true) {
            $table = $setup->getConnection()
                ->newTable($tableName)
                ->addColumn('id', Table::TYPE_INTEGER, 11, ['identity' => true,'unsigned' => true,'nullable' => false,'primary' => true], 'ID')
                ->addColumn('order_id', Table::TYPE_INTEGER, 11, ['nullable' => false])
                ->addColumn('pickup_location_id', Table::TYPE_INTEGER, 11, ['nullable' => false ])
                ->addColumn('cod_bara', Table::TYPE_TEXT, 50, ['nullable' => false ])
                ->addColumn('timestamp', Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => Table::TIMESTAMP_INIT ])
                ->addColumn('nume_dest', Table::TYPE_TEXT, 150, ['nullable' => false ])
                ->addColumn('judet_id', Table::TYPE_TEXT, 150, ['nullable' => false ])
                ->addColumn('judet_dest', Table::TYPE_TEXT, 150, ['nullable' => false ])
                ->addColumn('localitate_id', Table::TYPE_TEXT, 150, ['nullable' => false ])
                ->addColumn('localitate_dest', Table::TYPE_TEXT, 150, ['nullable' => false ])
                ->addColumn('adresa_dest', Table::TYPE_TEXT, 150, ['nullable' => false ])
                ->addColumn('contact_dest', Table::TYPE_TEXT, 150, ['nullable' => false ])
                ->addColumn('telefon_dest', Table::TYPE_TEXT, 150, ['nullable' => false ])
                ->addColumn('email_dest', Table::TYPE_TEXT, 150, ['nullable' => false ])
                ->addColumn('plicuri', Table::TYPE_INTEGER, 11, ['nullable' => false ])
                ->addColumn('colete', Table::TYPE_INTEGER, 11, ['nullable' => false ])
                ->addColumn('kilograme', Table::TYPE_INTEGER, 11, ['nullable' => false ])
                ->addColumn('valoare_declarata', Table::TYPE_FLOAT, [10, 2], ['nullable' => false ])
                ->addColumn('ramburs_numerar', Table::TYPE_FLOAT, [10, 2], ['nullable' => false ])
                ->addColumn('ramburs_cont', Table::TYPE_FLOAT, [10, 2], ['nullable' => false ])
                ->addColumn('ramburs_alt', Table::TYPE_TEXT, 250, ['nullable' => false ])
                ->addColumn('platitor_expeditie', Table::TYPE_INTEGER, 1, ['nullable' => false ])
                ->addColumn('livrare_sambata', Table::TYPE_INTEGER, 1, ['nullable' => false ])
                ->addColumn('livrare_dimineata', Table::TYPE_INTEGER, 1, ['nullable' => false ])
                ->addColumn('deschidere_colet', Table::TYPE_INTEGER, 1, ['nullable' => false ])
                ->addColumn('observatii', Table::TYPE_TEXT, 500, ['nullable' => false ])
                ->addColumn('continut', Table::TYPE_TEXT, 500, ['nullable' => false ])
                ->addColumn('status', Table::TYPE_INTEGER, 1, ['nullable' => false ]);
            $setup->getConnection()->createTable($table);
        }

        $setup->endSetup();
    }
}
