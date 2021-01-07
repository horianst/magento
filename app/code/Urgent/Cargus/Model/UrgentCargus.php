<?php

namespace Urgent\Cargus\Model;

use GuzzleHttp\Client;
use Magento\Framework\App\ObjectManager;

class UrgentCargus
{
    private $url;
    private $key;
    private $user;
    private $password;
    private $token;

    public $version = [
        'major'     => '1',
        'minor'     => '9',
        'revision'  => '4',
        'patch'     => '5',
        'stability' => '',
        'number'    => '',
    ];


    function __construct()
    {
        $objectManager = ObjectManager::getInstance();
        $this->url = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue(
            'carriers/urgentcargusshipping/urgent_cargus_api_url'
        );
        $this->key = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue(
            'carriers/urgentcargusshipping/urgent_cargus_api_key'
        );
        $this->user = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue(
            'carriers/urgentcargusshipping/urgent_cargus_username'
        );
        $this->password = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue(
            'carriers/urgentcargusshipping/urgent_cargus_password'
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

    /**
     * Fetch some data from API
     */
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
                        'path' => 'MG'.$this->version['major'].$this->version['minor'].$this->version['revision']
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
                        'path' => 'MG'.$this->version['major'].$this->version['minor'].$this->version['revision']
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
}
