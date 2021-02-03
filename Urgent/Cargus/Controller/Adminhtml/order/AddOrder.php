<?php

namespace Urgent\Cargus\Controller\Adminhtml\order;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class AddOrder
 */
class AddOrder extends Action implements CsrfAwareActionInterface
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var ResourceConnection
     */
    private $_resource;
    /**
     * @var StoreManagerInterface
     */
    private $_storeManager;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * AddOrder constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param ResourceConnection $resource
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ResourceConnection $resource,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;
        $this->_resource = $resource;
        $this->_storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
    }

    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    /**
     * @return Page
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('id');
        $orderName = $this->getRequest()->getParam('id_long');

        $connection = $this->_resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);

        $existing = $connection->fetchAll("SELECT `id` FROM `awb_expeditii` WHERE `order_id`='" . $orderName . "'");

        if ($existing) {
            echo 'old';
            die();
        }

        $query = "SELECT
                    so.weight,
                    so.order_currency_code,
                    so.created_at,
                    so.grand_total,
                    so.shipping_amount,
                    so.shipping_tax_amount,
                    so.total_paid,
                    soa.region,
                    soa.region_id,
                    soa.city,
                    soa.street,
                    soa.postcode,
                    soa.telephone,
                    soa.lastname,
                    soa.firstname,
                    soa.middlename,
                    soa.company,
                    soa.email,
                    sop.method,
                    GROUP_CONCAT(soi.name SEPARATOR '; ') AS continut
                FROM sales_order so
                LEFT JOIN sales_order_address soa ON so.shipping_address_id = soa.entity_id
                LEFT JOIN sales_order_item soi ON so.entity_id = soi.order_id
                LEFT JOIN sales_order_payment sop ON so.entity_id = sop.parent_id
                WHERE
                    so.entity_id = " . $orderId . " AND
                    soi.parent_item_id IS NULL AND
                    soa.address_type = 'shipping'
                GROUP BY so.increment_id,sop.method
                LIMIT 0,1";

        $data = $connection->fetchAll($query)[0];

        $base2ron = 1;
        $baseCode = $data['order_currency_code'];

        if ($baseCode != 'RON') {
            $defaultCurrency = $this->_storeManager->getStore()->getCurrentCurrencyCode();
            $allowedCurrencies = $this->_storeManager->getStore()->getAllowedCurrencies();

            $rates = $this->_storeManager->getStore()->getBaseCurrency()->getCurrencyRates(
                $defaultCurrency,
                array_values(
                    $allowedCurrencies
                )
            );
            $base2ron = $rates['RON'] / $rates[$baseCode];
        }

        $preferences = unserialize($this->scopeConfig->getValue('urgent/cargus/preferences'));

        if ($preferences['payer'] != 1 || ($preferences['fixed_cost_transport'] || $preferences['fixed_cost_transport'] == '0')) {
            $payer = 1;
            $valoareRamburs = round($data['grand_total'] * $base2ron, 2);
        } else {
            $payer = 2;
            $valoareRamburs = round(
                ($data['grand_total'] - $data['shipping_amount'] - $data['shipping_tax_amount']) * $base2ron,
                2
            );
        }

        if ($data['shipping_amount'] == 0) {
            $payer = 1;
        }

        if ($preferences['insurance'] == 0) {
            $DeclaredValue = round(($data['grand_total'] - $data['shipping_amount']) * $base2ron, 2);
        } else {
            $DeclaredValue = 0;
        }

        if (strpos($data['method'], 'cashondelivery') !== false) {
            if ($preferences['repayment'] == 1) {
                $CashRepayment = 0;
                $BankRepayment = round($valoareRamburs, 2);
            } else {
                $CashRepayment = round($valoareRamburs, 2);
                $BankRepayment = 0;
            }
        } else {
            $CashRepayment = 0;
            $BankRepayment = 0;
        }

        $query = "SELECT * FROM directory_country_region WHERE `country_id` = 'RO' AND `region_id` = '" . $data['region_id'] . "'";
        $judetResult = $connection->fetchAll($query);

        $weight = ($data['weight'] < 1 ? 1 : ceil($data['weight']));

        if ($weight > 1) {
            $no_envelopes = 0;
            $no_parcels = 1;
        } else {
            $no_envelopes = ($preferences['expedition_type'] == 'envelope' ? 1 : 0);
            $no_parcels = ($preferences['expedition_type'] != 'envelope' ? 1 : 0);
        }

        $query = "INSERT INTO `awb_expeditii` (
                            `order_id`,
                            `pickup_location_id`,
                            `cod_bara`,
                            `nume_dest`,
                            `judet_dest`,
                            `localitate_dest`,
                            `adresa_dest`,
                            `contact_dest`,
                            `telefon_dest`,
                            `email_dest`,
                            `plicuri`,
                            `colete`,
                            `kilograme`,
                            `valoare_declarata`,
                            `ramburs_numerar`,
                            `ramburs_cont`,
                            `platitor_expeditie`,
                            `livrare_sambata`,
                            `livrare_dimineata`,
                            `deschidere_colet`,
                            `observatii`,
                            `continut`,
                            `status`
                        ) VALUES (
                            '" . $this->clear($orderName) . "',
                            '" . $preferences['collect_point'] . "',
                            '',
                            '" . ($this->clear($data['company']) ? $this->clear($data['company']) : $this->clear(
                    $data['firstname']
                ) . ' ' . $this->clear($data['lastname'])) . "',
                            '" . $this->clear($judetResult[0]['code']) . "',
                            '" . $this->clear($data['city']) . "',
                            '" . $this->clear($data['street']) . "',
                            '" . $this->clear($data['firstname']) . ' ' . $this->clear($data['lastname']) . "',
                            '" . $this->clear($data['telephone']) . "',
                            '" . $this->clear($data['email']) . "',
                            '" . $no_envelopes . "',
                            '" . $no_parcels . "',
                            '" . $weight . "',
                            '" . $DeclaredValue . "',
                            '" . $CashRepayment . "',
                            '" . $BankRepayment . "',
                            '" . $payer . "',
                            '" . $preferences['saturday_delivery'] . "',
                            '" . $preferences['morning_delivery'] . "',
                            '" . $preferences['open_package'] . "',
                            '',
                            '" . $this->clear($data['continut']) . "',
                            '0'
                        );";
        $connection->query($query);

        $out = $connection->fetchAll(
            "SELECT * FROM `awb_expeditii` WHERE `order_id` = '" . $this->clear($orderName) . "' LIMIT 1"
        );
        if (count($out) > 0) {
            echo 'ok';
        } else {
            echo 'bad';
        }
    }

    public function clear($var)
    {
        return addslashes(trim($var));
    }
}
