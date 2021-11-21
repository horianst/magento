<?php

namespace Urgent\Cargus\Model;

use GuzzleHttp\Client;
use Magento\Framework\App\ObjectManager;

class UrgentCargus
{
    public $version = [
        'major' => '1',
        'minor' => '9',
        'revision' => '4',
        'patch' => '5',
        'stability' => '',
        'number' => '',
    ];
    private $url;
    private $key;
    private $user;
    private $password;
    private $token;

    function __construct()
    {
        $objectManager = ObjectManager::getInstance();
        $this->url = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue(
            'carriers/customshipping/urgent_cargus_api_url'
        );
        $this->key = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue(
            'carriers/customshipping/urgent_cargus_api_key'
        );
        $this->user = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue(
            'carriers/customshipping/urgent_cargus_username'
        );
        $this->password = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue(
            'carriers/customshipping/urgent_cargus_password'
        );

        $this->token = $this->login();
    }

    protected function login()
    {
        try {
            $client = new Client();
            $response = $client->request(
                'POST',
                $this->url . '/LoginUser',
                [
                    'headers' => [
                        'Ocp-Apim-Subscription-Key' => $this->key,
                        'Content-Type' => 'application/json'
                    ],
                    'json' => [
                        'UserName' => $this->user,
                        'Password' => $this->password
                    ]
                ]
            );

            return json_decode($response->getBody());
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getPickupPoints()
    {
        try {
            $client = new Client();
            $response = $client->request(
                'GET',
                $this->url . '/PickupLocations',
                [
                    'headers' => [
                        'Ocp-Apim-Subscription-Key' => $this->key,
                        'Authorization' => 'Bearer ' . $this->token,
                        'Content-Type' => 'application/json'
                    ]
                ]
            );
            return json_decode($response->getBody());
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getAwbs($orderId)
    {
        try {
            $client = new Client();
            $response = $client->request(
                'GET',
                $this->url . '/Awbs',
                [
                    'headers' => [
                        'Ocp-Apim-Subscription-Key' => $this->key,
                        'Authorization' => 'Bearer ' . $this->token,
                        'Content-Type' => 'application/json'
                    ],
                    'query' => [
                        'orderId' => $orderId,
                    ]
                ]
            );
            return json_decode($response->getBody());
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getOrders($locationId, $status = 1, $itemsPerPage = 100)
    {
        try {
            $client = new Client();
            $response = $client->request(
                'GET',
                $this->url . '/Orders',
                [
                    'headers' => [
                        'Ocp-Apim-Subscription-Key' => $this->key,
                        'Authorization' => 'Bearer ' . $this->token,
                        'Content-Type' => 'application/json'
                    ],
                    'query' => [
                        'locationId' => $locationId,
                        'status' => $status,
                        'pageNumber' => 1,
                        'itemsPerPage' => $itemsPerPage,
                    ]
                ]
            );
            return json_decode($response->getBody());
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getOrder($id)
    {
        try {
            $client = new Client();
            $response = $client->request(
                'GET',
                $this->url . '/Awbs',
                [
                    'headers' => [
                        'Ocp-Apim-Subscription-Key' => $this->key,
                        'Authorization' => 'Bearer ' . $this->token,
                        'Content-Type' => 'application/json'
                    ],
                    'query' => [
                        'orderId' => $id,
                    ]
                ]
            );
            return json_decode($response->getBody());
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getAwb($id)
    {
        try {
            $client = new Client();
            $response = $client->request(
                'GET',
                $this->url . '/Awbs',
                [
                    'headers' => [
                        'Ocp-Apim-Subscription-Key' => $this->key,
                        'Authorization' => 'Bearer ' . $this->token,
                        'Content-Type' => 'application/json'
                    ],
                    'query' => [
                        'barCode' => $id,
                    ]
                ]
            );
            return json_decode($response->getBody());
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function execute()
    {
        try {
            $client = new Client();
            $response = $client->request(
                'GET',
                $this->url . '/PickupLocations',
                [
                    'headers' => [
                        'Ocp-Apim-Subscription-Key' => $this->key,
                        'Authorization' => 'Bearer ' . $this->token,
                        'Content-Type' => 'application/json'
                    ]
                ]
            );
            return json_decode($response->getBody());
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function ShippingCalculation($fields)
    {
        try {
            $client = new Client();
            $response = $client->request(
                'POST',
                $this->url . '/ShippingCalculation',
                [
                    'headers' => [
                        'Ocp-Apim-Subscription-Key' => $this->key,
                        'Authorization' => 'Bearer ' . $this->token,
                        'Content-Type' => 'application/json',
                        'path' => 'MG' . $this->version['major'] . $this->version['minor'] . $this->version['revision']
                    ],
                    'json' => $fields
                ]
            );
            return json_decode($response->getBody());
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function validateAwb($fields)
    {
        try {
            $client = new Client();
            $response = $client->request(
                'POST',
                $this->url . '/Awbs',
                [
                    'headers' => [
                        'Ocp-Apim-Subscription-Key' => $this->key,
                        'Authorization' => 'Bearer ' . $this->token,
                        'Content-Type' => 'application/json',
                        'path' => 'MG' . $this->version['major'] . $this->version['minor'] . $this->version['revision']
                    ],
                    'json' => $fields
                ]
            );
            return json_decode($response->getBody());
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function deleteAwb($code)
    {
        try {
            $client = new Client();
            $response = $client->request(
                'DELETE',
                $this->url . '/Awbs',
                [
                    'headers' => [
                        'Ocp-Apim-Subscription-Key' => $this->key,
                        'Authorization' => 'Bearer ' . $this->token,
                        'Content-Type' => 'application/json',
                        'path' => 'MG' . $this->version['major'] . $this->version['minor'] . $this->version['revision']
                    ],
                    'query' => [
                        'barCode' => $code,
                    ]
                ]
            );
            return json_decode($response->getBody());
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getCounties()
    {
        try {
            $client = new Client();
            $response = $client->request(
                'GET',
                $this->url . '/Counties',
                [
                    'headers' => [
                        'Ocp-Apim-Subscription-Key' => $this->key,
                        'Authorization' => 'Bearer ' . $this->token,
                        'Content-Type' => 'application/json',
                    ],
                    'query' => [
                        'countryId' => 1,
                    ]
                ]
            );
            return json_decode($response->getBody());
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getCities($countyId)
    {
        try {
            $client = new Client();
            $response = $client->request(
                'GET',
                $this->url . '/Localities',
                [
                    'headers' => [
                        'Ocp-Apim-Subscription-Key' => $this->key,
                        'Authorization' => 'Bearer ' . $this->token,
                        'Content-Type' => 'application/json',
                    ],
                    'query' => [
                        'countryId' => 1,
                        'countyId' => $countyId,
                    ]
                ]
            );
            return json_decode($response->getBody());
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getPudoPoints(){
        // function to get the list of all Pickup Locations, API does not have parameters for scoping to CityId :(
        // scoping needs to be done in frontend
        // https://urgentcargus.azure-api.net/api/PudoPoints

        /*
         * IMPORTANT informatiom
         *
         * "id" from response is used to generate AWB
         * "ServiceCOD"  from response determines if the PUDO can have cash on delivery (boolean)
         * "PaymentType" from response is: 1 - no method available, 2 - CARD, 3 - CASH + CARD, 4 - CASH
         *
         * */

        try {
            $client = new Client();
            $response = $client->request(
                'GET',
                $this->url . '/PudoPoints',
                [
                    'headers' => [
                        'Ocp-Apim-Subscription-Key' => $this->key,
                        'Authorization' => 'Bearer ' . $this->token,
                        'Content-Type' => 'application/json'
                    ]
                ]
            );
            return json_decode($response->getBody());
        } catch (\Exception $e) {
            return $e->getMessage();
        }

    }

    public function printAwb($barCodes, $format)
    {
        try {
            $client = new Client();
            $response = $client->request(
                'GET',
                $this->url . '/AwbDocuments',
                [
                    'headers' => [
                        'Ocp-Apim-Subscription-Key' => $this->key,
                        'Authorization' => 'Bearer ' . $this->token,
                        'Content-Type' => 'application/json',
                    ],
                    'query' => [
                        'type' => 'PDF',
                        'format' => $format,
                        'barCodes' => $barCodes,
                    ]
                ]
            );
            return json_decode($response->getBody());
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function printSheet($orderId)
    {
        try {
            $client = new Client();
            $response = $client->request(
                'GET',
                $this->url . '/OrderDocuments',
                [
                    'headers' => [
                        'Ocp-Apim-Subscription-Key' => $this->key,
                        'Authorization' => 'Bearer ' . $this->token,
                        'Content-Type' => 'application/json',
                    ],
                    'query' => [
                        'orderId' => $orderId,
                        'docType' => 0,
                    ]
                ]
            );
            return json_decode($response->getBody());
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function sendOrder($locationId, $from, $to)
    {
        try {
            $query = [
                'locationId' => $locationId,
                'PickupStartDate' => date('Y-m-d H:i:s', strtotime($from)),
                'PickupEndDate' => date('Y-m-d H:i:s', strtotime($to)),
                'action' => 1,
            ];

            $client = new Client();
            $response = $client->request(
                'PUT',
                $this->url . '/Orders',
                [
                    'headers' => [
                        'Ocp-Apim-Subscription-Key' => $this->key,
                        'Authorization' => 'Bearer ' . $this->token,
                        'Content-Type' => 'application/json',
                    ],
                    'query' => $query
                ]
            );
            return json_decode($response->getBody());
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
