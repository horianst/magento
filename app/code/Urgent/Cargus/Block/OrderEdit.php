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
        return $connection->fetchAll(
            "SELECT * FROM `awb_expeditii` WHERE status = 0 AND id = '" . addslashes(
                $this->getRequest()->getParam('id')
            ) . "'"
        );
    }

    public function getCounties()
    {
        $urgentCargus = new UrgentCargus();
        return $urgentCargus->getCounties();
    }

    public function getCities($countyCode)
    {
        $cities = [];

        $connection = $this->_resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);
        $county = $connection->fetchAll(
            "SELECT * FROM directory_country_region WHERE country_id = 'RO' AND code = '" . $countyCode . "'"
        )[0];

        $preferences = unserialize($this->scopeConfig->getValue('urgent/cargus/preferences'));

        if ($preferences['cities'] == 1) {
            $localities = $connection->fetchAll(
                "SELECT l.name, l.in_network, l.extra_km FROM awb_localities l LEFT JOIN awb_counties c ON c.county_id = l.county_id WHERE c.abbreviation = '" . $county['code'] . "' ORDER BY l.name ASC"
            );
            foreach ($localities as $locality) {
                $index = $locality['in_network'] ? 0 : $locality['extra_km'];
                $cities[$index] = $locality['name'];
            }
        } else {
            $urgentCargus = new UrgentCargus();
            $countiesList = $urgentCargus->getCounties();

            $counties = [];

            foreach ($countiesList as $county) {
                $counties[strtolower($county->Abbreviation)] = $county->CountyId;
            }

            $localities = $urgentCargus->getCities($counties[strtolower($countyCode)]);

            foreach ($localities as $locality) {
                $index = $locality->InNetwork ? 0 : $locality->ExtraKm;
                $cities[$index] = $locality->Name;
            }
        }

        return $cities;
    }
}
