<?php

namespace Urgent\Cargus\Block;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\View\Element\Template;
use Urgent\Cargus\Model\UrgentCargus;

class OrderSend extends Template
{
    private FormKey $formKey;
    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;
    private array $data;
    /**
     * @var ResourceConnection
     */
    private ResourceConnection $_resource;

    public function __construct(Context $context, FormKey $formKey, ScopeConfigInterface $scopeConfig, ResourceConnection $resource, array $data = [])
    {
        parent::__construct($context, $data);
        $this->formKey = $formKey;
        $this->scopeConfig = $scopeConfig;
        $this->data = $data;
        $this->_resource = $resource;
    }

    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

    public function getSheet()
    {
        return $this->data;
    }
}
