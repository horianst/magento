<?php

namespace Urgent\Cargus\Block;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\View\Element\Template;
use Urgent\Cargus\Model\UrgentCargus;

class HistoryOrder extends Template
{

    protected $request;

    public function __construct(Context $context, array $data = [], Http $request)
    {
        parent::__construct($context, $data);
        $this->request = $request;
    }


    public function getOrder()
    {
        $id = $this->request->getParam('id');
        $urgentCargus = new UrgentCargus();
        return $urgentCargus->getOrder($id);
    }

    public function getOrderId()
    {
        return $this->request->getParam('id');
    }
}
