<?php

namespace Urgent\Cargus\Block;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\View\Element\Template;
use Urgent\Cargus\Model\UrgentCargus;

class Order extends Template
{
    /**
     * @var FormKey
     */
    private $formKey;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var array
     */
    private $data;
    /**
     * @var ResourceConnection
     */
    private $_resource;

    public function __construct(
        Context $context,
        FormKey $formKey,
        ScopeConfigInterface $scopeConfig,
        ResourceConnection $resource,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->formKey = $formKey;
        $this->scopeConfig = $scopeConfig;
        $this->data = $data;
        $this->_resource = $resource;
    }

    public function getWaitningAwbs()
    {
        $connection = $this->_resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);

        $awbExpeditii = $connection->getTableName('awb_expeditii');
        return $connection->fetchAll('SELECT * FROM `' . $awbExpeditii . '`  WHERE status = 0 ORDER BY timestamp DESC');
    }

    public function getConfigParams()
    {
        if (empty($this->getRequest()->getPostValue())) {
            return unserialize($this->scopeConfig->getValue('urgent/cargus/preferences'));
        } else {
            return $this->getRequest()->getPostValue();
        }
    }

    public function getPickupPoints()
    {
        $urgentCargus = new UrgentCargus();
        return $urgentCargus->getPickupPoints();
    }

    public function getPudoPoints()
    {
        $urgentCargus = new UrgentCargus();
        return $urgentCargus->getPudoPoints();
    }

    public function getCurrentOrder($locationId)
    {
        $urgentCargus = new UrgentCargus();
        $order = $urgentCargus->getOrders($locationId, 0, 1);

        $awbList = [];
        if (!is_null($order)) {
            $awbs = $urgentCargus->getAwbs($order->OrderId);
            if (!is_null($awbs)) {
                foreach ($awbs as $awb) {
                    if ($awb->Status != 'Deleted') {
                        $awbList[] = $awb;
                    }
                }
            }
        }
        return $awbList;
    }

    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }
}
