<?php

namespace Urgent\Cargus\Model\Carrier;

use Magento\Directory\Model\Currency;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Urgent\Cargus\Model\UrgentCargus;

/**
 * Custom shipping model
 */
class Urgentcargusshipping extends AbstractCarrier implements CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'customshipping';

    /**
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    private $rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    private $rateMethodFactory;
    /**
     * @var Currency
     */
    private $currenciesModel;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        Currency $currencyModel,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);

        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->currenciesModel = $currencyModel;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Custom Shipping Rates Collector
     *
     * @param RateRequest $request
     * @return \Magento\Shipping\Model\Rate\Result|bool
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->rateResultFactory->create();

        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->rateMethodFactory->create();

        $method->setCarrier($this->_code);
        $method->setCarrierTitle('Cargus');

        $method->setMethod($this->_code);
        $method->setMethodTitle('Standard');

        $shippingCost = $this->getShippingCost($request);

        if($shippingCost === false){
            return false;
        }

        $method->setPrice($shippingCost);
        $method->setCost($shippingCost);

        $result->append($method);

        return $result;
    }

    public function getShippingCost(RateRequest $request)
    {
        if (strlen(trim($request->getDestCity())) < 1 || strlen(trim($request->getDestRegionCode())) < 1) {
            return false;
        }

        // calculeaza cursul de schimb valutar
        $base2ron = 1;
        $ron2base = 1;
        $baseCode = $this->storeManager->getStore()->getBaseCurrencyCode();
        if (strtolower($baseCode) != 'ron') {
            $allowedCurrencies = $this->currenciesModel->getConfigAllowCurrencies();
            $baseRates = $this->currenciesModel->getCurrencyRates($baseCode, array_values($allowedCurrencies));
            $rates = array();
            foreach ($baseRates as $k => $v) {
                $rates[strtolower($k)] = $v;
            }
            if (!isset($rates['ron'])) {
                return false;
            }
            $base2ron = $rates['ron'] / $rates[strtolower($baseCode)];
            $ron2base = $rates[strtolower($baseCode)] / $rates['ron'];
        }

        // elimina produsele care nu necesita transport
        if ($request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                if ($item->getParentItem()) {
                    continue;
                }
                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    foreach ($item->getChildren() as $child) {
                        if ($child->getProduct()->isVirtual()) {
                            $request->setPackageValue($request->getPackageValue() - $child->getBaseRowTotal());
                        }
                    }
                } elseif ($item->getProduct()->isVirtual()) {
                    $request->setPackageValue($request->getPackageValue() - $item->getBaseRowTotal());
                }
            }
        }

        $params = unserialize($this->scopeConfig->getValue('urgent/cargus/preferences'));

        // obtine totalul cosului din request
        $valoare = $request->getPackageValue() * $base2ron;

        // stabilesc daca se ofera transport gratuit
        if ($params['max_free_transport'] > 0 && $valoare > $params['max_free_transport']) {
            return 0;
        }

        // stabilesc daca exista un cost fix configurat si setez pretul in functie de plafonul pentru transport gratuit
        if ($params['fixed_cost_transport'] || $params['fixed_cost_transport'] == '0') {
            $costfix = $params['fixed_cost_transport'];
            return $costfix;
        }

        // calculeaza transportul
        if ($params['insurance'] == '1') {
            $DeclaredValue = round($valoare, 2);
        } else {
            $DeclaredValue = 0;
        }

        if ($params['repayment'] == 1) {
            $CashRepayment = 0;
            $BankRepayment = round($valoare, 2);
        } else {
            $CashRepayment = round($valoare, 2);
            $BankRepayment = 0;
        }

        if (isset($_POST['payment']['method'])) {
            if (strpos($_POST['payment']['method'], 'cashondelivery') !== false) {
            } else {
                $CashRepayment = 0;
                $BankRepayment = 0;
            }
        }

        $urgentCargus = new UrgentCargus();

        // UC punctul de ridicare default
        $location = array();
        $pickups = $urgentCargus->execute();

        if (is_null($pickups)) {
            return false;
        }

        foreach ($pickups as $pick) {
            if ($params['collect_point'] == $pick->LocationId) {
                $location = $pick;
            }
        }

        $weight = ceil($request->getPackageWeight());

        if ($weight > 1) {
            $no_envelopes = 0;
            $no_parcels = 1;
        } else {
            $no_envelopes = ($params['expedition_type'] == 'envelope' ? 1 : 0);
            $no_parcels = ($params['expedition_type'] != 'envelope' ? 1 : 0);
        }

        // UC shipping calculation
        $fields = array(
            'FromLocalityId' => $location->LocationId,
            'ToLocalityId' => 0,
            'FromCountyName' => '',
            'FromLocalityName' => '',
            'ToCountyName' => $request->getDestRegionCode(),
            'ToLocalityName' => $request->getDestCity(),
            'Parcels' => $no_parcels,
            'Envelopes' => $no_envelopes,
            'TotalWeight' => $weight,
            'DeclaredValue' => $DeclaredValue,
            'CashRepayment' => $CashRepayment,
            'BankRepayment' => $BankRepayment,
            'OtherRepayment' => '',
            'PaymentInstrumentId' => 0,
            'PaymentInstrumentValue' => 0,
            'OpenPackage' => ($params['open_package'] != 1 ? false : true),
            'SaturdayDelivery' => ($params['saturday_delivery'] != 1 ? false : true),
            'MorningDelivery' => ($params['morning_delivery'] != 1 ? false : true),
            'ShipmentPayer' => ($params['payer'] != 'recipient' ? 1 : 2)
        );

        $calculate = $urgentCargus->ShippingCalculation($fields);

        if (is_null($calculate) || isset($calculate->Error)) {
            return false;
        }

        $total = round($calculate->GrandTotal * $ron2base, 2);

        return $total;
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => 'Standard'];
    }
}