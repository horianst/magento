<?php
namespace Urgent\Cargus\Model\ResourceModel\AWBExpeditii;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Remittance File Collection Constructor
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Urgent\Cargus\Model\AWBExpeditii', 'Urgent\Cargus\Model\ResourceModel\AWBExpeditii');
    }
}
