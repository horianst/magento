<?php

namespace Urgent\Cargus\Block;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\View\Element\Template;
use Urgent\Cargus\Model\UrgentCargus;

class History extends Template
{
    private FormKey $formKey;
    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;
    private array $data;

    public function __construct(Context $context, FormKey $formKey, ScopeConfigInterface $scopeConfig, array $data = [])
    {
        parent::__construct($context, $data);
        $this->formKey = $formKey;
        $this->scopeConfig = $scopeConfig;
        $this->data = $data;
    }

    public function getPickupPoints()
    {
        $urgentCargus = new UrgentCargus();
        return $urgentCargus->getPickupPoints();
    }

    public function getConfigParams()
    {
        return unserialize($this->scopeConfig->getValue('urgent/cargus/preferences'));
    }

    public function getOrders($LocationId = null)
    {
        $urgentCargus = new UrgentCargus();
        return $urgentCargus->getOrders($LocationId);
    }
}
