<?php

namespace Urgent\Cargus\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class AWBLocalities extends AbstractDb
{
    /**
     * Post Abstract Resource Constructor
     * @return void
     */
    protected function _construct()
    {
        $this->_init('awb_localities', 'id');
    }
}
