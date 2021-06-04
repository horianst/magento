<?php

namespace Urgent\Cargus\Controller\Adminhtml\order;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\View\Result\Page;
use Urgent\Cargus\Model\UrgentCargus;

/**
 * Class Wait
 */
class Wait extends Action implements CsrfAwareActionInterface
{

    /**
     * @var ResourceConnection
     */
    private $_resource;

    /**
     * Wait constructor.
     *
     * @param Context $context
     * @param ResourceConnection $resource
     */
    public function __construct(Context $context, ResourceConnection $resource)
    {
        parent::__construct($context);
        $this->_resource = $resource;
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
     * Load the page defined in view/adminhtml/layout/order_index.xml
     *
     * @return Page
     */
    public function execute()
    {
        $awbs = $this->getRequest()->getParam('awb');

        if ($awbs) {
            $connection = $this->_resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);

            $data = $connection->fetchAll(
                'SELECT * FROM awb_expeditii WHERE status = 0 AND order_id IN(' . implode(
                    ', ',
                    $awbs
                ) . ') ORDER BY timestamp DESC'
            );


            $go = true;

            foreach ($data as $item) {
                if($item['lungime'] < 0.1 || $item['latime'] < 0.1 || $item['inaltime'] < 0.1){
                    $this->messageManager->addErrorMessage((__('Va rugam sa introduceti dimensiunile coletelor!')));
                    $go = false;
                    break;
                }

                if(!$item['cod_postal']){
                    $this->messageManager->addErrorMessage((__('Va rugam sa introduceti codul postal al destinatarului!')));
                    $go = false;
                    break;
                }
            }

            if($go){
                $errors = [];
                $done = 0;
                foreach ($data as $item) {
                    $fields = [
                        'Sender' => [
                            'LocationId' => $item['pickup_location_id']
                        ],
                        'Recipient' => [
                            'LocationId' => null,
                            'Name' => $item['nume_dest'],
                            'CountyId' => null,
                            'CountyName' => $item['judet_dest'],
                            'LocalityId' => null,
                            'LocalityName' => $item['localitate_dest'],
                            'StreetId' => null,
                            'StreetName' => '-',
                            'AddressText' => $item['adresa_dest'],
                            'ContactPerson' => $item['contact_dest'],
                            'PhoneNumber' => $item['telefon_dest'],
                            'Email' => $item['email_dest'],
                            "CodPostal" => $item['cod_postal'],
                        ],
                        'Parcels' => $item['colete'],
                        'Envelopes' => $item['plicuri'],
                        'TotalWeight' => $item['kilograme'],
                        'DeclaredValue' => $item['valoare_declarata'],
                        'CashRepayment' => $item['ramburs_numerar'],
                        'BankRepayment' => $item['ramburs_cont'],
                        'OtherRepayment' => $item['ramburs_alt'],
                        'OpenPackage' => $item['deschidere_colet'] == 1 ? true : false,
                        'ShipmentPayer' => $item['platitor_expeditie'],
                        'MorningDelivery' => $item['livrare_dimineata'] == 1 ? true : false,
                        'SaturdayDelivery' => $item['livrare_sambata'] == 1 ? true : false,
                        'Observations' => $item['observatii'],
                        'PackageContent' => $item['continut'],
                        'CustomString' => $item['order_id'],
                        "ParcelCodes" => [
                            [
                                "Code"=> 0,
                                "Type"=>   $item['colete'] > 0 ? 1 : 0,
                                "Weight" => $item['kilograme'],
                                "Length" => $item['lungime'],
                                "Width" => $item['latime'],
                                "Height" => $item['inaltime'],
                                "ParcelContent" => $item['continut']
                            ]
                        ]
                    ];

                    $params = unserialize($this->scopeConfig->getValue('urgent/cargus/preferences'));

                    if($params['service'] == 1) {
                        if($item['kilograme'] <= 31){
                            $fields['ServiceId'] = 34;
                        } elseif ($item['kilograme'] <= 50){
                            $fields['ServiceId'] = 35;
                        } else {
                            $fields['ServiceId'] = 36;
                        }
                    }

                    $urgentCargus = new UrgentCargus();
                    $codBara = $urgentCargus->validateAwb($fields);

                    if (is_array($codBara)) {
                        if (isset($codBara['error'])) {
                            $errors[] = $item['order_id'] . ': ' . $codBara;
                        }
                    } else {
                        if ($codBara != '') {
                            $query = "UPDATE `awb_expeditii` SET `cod_bara` = '" . $codBara . "', `status` = '1' WHERE `order_id` = '" . $item['order_id'] . "'";
                            $connection->query($query);
                            ++$done;
                        } else {
                            $errors[] = 'Unknown error!';
                        }
                    }
                }

                if (count($data) == $done) {
                    $this->messageManager->addNoticeMessage(__('Toate AWB-urile bifate au fost validate!'));
                } elseif (count($data) == count($errors)) {
                    $this->messageManager->addErrorMessage(
                        (__('Niciun AWB bifat nu a putut fi validat! ' . implode(', ', $errors)))
                    );
                } else {
                    $this->messageManager->addErrorMessage(
                        (__(
                            count($errors) . ' ' . (count($errors) == 1 ? 'AWB' : 'AWB-uri') . ' ' . (count(
                                $data
                            ) > 1 ? 'din cele ' . count($data) . ' bifate' : '') . ' nu ' . (count(
                                $errors
                            ) == 1 ? 'a' : 'au') . ' putut fi ' . (count(
                                $errors
                            ) == 1 ? 'validat' : 'validate') . '! ' . implode(', ', $errors)
                        ))
                    );
                }
            }
        }

        $this->_redirect('cargus/order/index');
    }
}
