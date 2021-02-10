<?php

namespace Urgent\Cargus\Model\Carrier;

use Magento\Directory\Model\Currency;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Urgent\Cargus\Model\UrgentCargus;

/**
 * Urgentcargusshipping model
 */
class Urgentcargusshipping extends AbstractCarrier implements CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'urgentcargusshipping';

    /**
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * @var ResultFactory
     */
    private $rateResultFactory;

    /**
     * @var MethodFactory
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
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        Currency $currencyModel,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);

        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->currenciesModel = $currencyModel;
        $this->storeManager = $storeManager;
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
        $result->append($this->calculeazaTransport($request));

        return $result;
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }

    public function isTrackingAvailable() {
        return true;
    }

    public function calculeazaTransport(Mage_Shipping_Model_Rate_Request $request) {

        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->rateMethodFactory->create();
        $method->setCarrier($this->_code);
        $method->setCarrierTitle('Cargus');
        $method->setMethod($this->_code);
        $method->setMethodTitle('Standard');

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

        $params =  unserialize($this->scopeConfig->getValue('urgent/cargus/preferences'));

        // obtine totalul cosului din request
        $valoare = $request->getPackageValue() * $base2ron;

        // stabilesc daca se ofera transport gratuit
        if ($params['max_free_transport'] > 0 && $valoare > $params['max_free_transport']) {
            $method->setCost(0);
            $method->setPrice(0);
            return $method;
        }

        // stabilesc daca exista un cost fix configurat si setez pretul in functie de plafonul pentru transport gratuit
        if ($params['fixed_cost_transport'] || $params['fixed_cost_transport'] == '0') {
            $costfix = $params['fixed_cost_transport'];
            $method->setCost($costfix);
            $method->setPrice($costfix);
            return $method;
        }

        // calculeaza transportul
        if ($params['insurance'] == '1') {
            $DeclaredValue = round($valoare, 2);
        } else {
            $DeclaredValue = 0;
        }
        if ($params['repayment'] == 'bank') {
            $CashRepayment = 0;
            $BankRepayment = round($valoare, 2);
        } else {
            $CashRepayment = round($valoare, 2);
            $BankRepayment = 0;
        }
        if (isset($_POST['payment']['method'])) {
            if (strpos($_POST['payment']['method'], 'cashondelivery') !== false) {} else {
                $CashRepayment = 0;
                $BankRepayment = 0;
            }
        }

        $urgentCargus = new UrgentCargus();

        // UC punctul de ridicare default
        $location = array();
        $pickups = $urgentCargus->execute();
        if (is_null($pickups)) {
            die('Nu exista niciun punct de ridicare asociat acestui cont!');
        }
        foreach ($pickups as $pick) {
            if ($params['collect_point'] == $pick['LocationId']) {
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
            'FromLocalityId' => $location['LocalityId'],
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
            'ShipmentPayer' => ($params['payer'] != 'recipient' ? 1 : 2),
            'ServiceId' => ($params['payer'] != 'recipient' ? 1 : 4),
            'PriceTableId' => null
        );

        $calculate = $urgentCargus->ShippingCalculation($fields);

        if (is_null($calculate) || (is_array($calculate) && isset($calculate['Error']))) {
            echo '<pre>';
            print_r($calculate);
            print_r($fields);
            die();
        }

        $total = round($calculate['GrandTotal'] * $ron2base, 2);
        if ($request->getFreeShipping()) {
            $method->setCost(0);
            $method->setPrice(0);
        } else {
            $method->setCost($total);
            $method->setPrice($total);
        }
        return $method;
    }
}
