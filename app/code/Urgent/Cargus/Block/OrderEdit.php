<?php

namespace Urgent\Cargus\Block;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\View\Element\Template;
use Urgent\Cargus\Model\UrgentCargus;

class OrderEdit extends Template
{
    private $formKey;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    private $data;
    /**
     * @var ResourceConnection
     */
    private $_resource;

    public function __construct(Context $context, FormKey $formKey, ScopeConfigInterface $scopeConfig, ResourceConnection $resource, array $data = [])
    {
        parent::__construct($context, $data);
        $this->formKey = $formKey;
        $this->scopeConfig = $scopeConfig;
        $this->data = $data;
        $this->_resource = $resource;
    }

    public function getPickupPoints()
    {
        $urgentCargus = new UrgentCargus();
        return $urgentCargus->getPickupPoints();
    }

    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

    public function getWaitAwb()
    {
        $connection = $this->_resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);
        return $connection->fetchAll("SELECT * FROM `awb_expeditii` WHERE status = 0 AND id = '".addslashes($this->getRequest()->getParam('id'))."'");
    }

    public function getCounties()
    {
        $urgentCargus = new UrgentCargus();
        return $urgentCargus->getCounties();
    }

    public function getCities($countyId)
    {
        $urgentCargus = new UrgentCargus();
        return $urgentCargus->getCities($countyId);
    }
}
