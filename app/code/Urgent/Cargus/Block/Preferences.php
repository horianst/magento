<?php

namespace Urgent\Cargus\Block;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\View\Element\Template;
use Urgent\Cargus\Model\UrgentCargus;

class Preferences extends Template
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



    /**
     * Get form action URL for POST booking request
     *
     * @return string
     */
    public function getFormAction()
    {
        return '/cargus/preferences/index';
    }

    public function checkCredentials()
    {
        //todo check credentials
        //login in the U.C. api and check if the user is accepted
        //in original code is commented out and the method returns true

        return true;
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

    public function getConfigParams()
    {
        if(empty($this->getRequest()->getPostValue())){
            return unserialize($this->scopeConfig->getValue('urgent/cargus/preferences'));
        } else {
            return $this->getRequest()->getPostValue();
        }
    }
}
