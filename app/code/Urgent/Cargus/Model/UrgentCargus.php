<?php

namespace Urgent\Cargus\Model;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use \Magento\Framework\App\ObjectManager;

class UrgentCargus
{
    private $url;
    private $key;
    private $user;
    private $password;
    private $token;


    function __construct() {
        $objectManager = ObjectManager::getInstance();
        $this->url = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('carriers/urgentcargusshipping/urgent_cargus_api_url');
        $this->key = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('carriers/urgentcargusshipping/urgent_cargus_api_key');
        $this->user = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('carriers/urgentcargusshipping/urgent_cargus_username');
        $this->password = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('carriers/urgentcargusshipping/urgent_cargus_password');

        $this->token = $this->login();

    }

    protected function login()
    {
        try {
            $client = new Client();
            $response = $client->request('POST', $this->url . '/LoginUser', [
                'headers' => [
                    'Ocp-Apim-Subscription-Key' => $this->key,
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'UserName' => $this->user,
                    'Password' =>  $this->password
                ]
            ]);

            return json_decode($response->getBody());
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getPickupPoints() {
        try{
            $client = new Client();
            $response = $client->request('GET', $this->url . '/PickupLocations', [
                'headers' => [
                    'Ocp-Apim-Subscription-Key' => $this->key,
                    'Authorization' => 'Bearer ' . $this->token,
                    'Content-Type' => 'application/json'
                ]
            ]);
            return json_decode($response->getBody());
        } catch (\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * Fetch some data from API
     */
    public function execute()
    {
        try{
            $client = new Client();
            $response = $client->request('GET', $this->url . '/PickupLocations', [
                'headers' => [
                    'Ocp-Apim-Subscription-Key' => $this->key,
                    'Authorization' => 'Bearer ' . $this->token,
                    'Content-Type' => 'application/json'
                ]
            ]);
            return json_decode($response->getBody());
        } catch (\Exception $e){
            return $e->getMessage();
        }
    }
}


//function CallMethod($function, $parameters = '', $verb, $token = null) {
//    $json = json_encode($parameters);
//
//    curl_setopt($this->curl, CURLOPT_POSTFIELDS, $json);
//    curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $verb);
//    curl_setopt($this->curl, CURLOPT_URL, $this->url . '/' . $function);
//
//    if ($function == 'LoginUser') {
//        $headers = array (
//            'Ocp-Apim-Subscription-Key: '.$this->key,
//            'Ocp-Apim-Trace: true',
//            'Content-Type: application/json',
//            'ContentLength: '.strlen($json)
//        );
//    } else {
//        $headers = array (
//            'Ocp-Apim-Subscription-Key: '.$this->key,
//            'Ocp-Apim-Trace: true',
//            'Authorization: Bearer '.$token,
//            'Content-Type: application/json',
//            'Content-Length: '.strlen($json)
//        );
//        if ($function == 'Awbs' && $verb == 'POST') {
//            $ver = Mage::getVersionInfo();
//            $headers[] = 'path: MG'.$ver['major'].$ver['minor'].$ver['revision'];
//        }
//    }
//
//    curl_setopt(
//        $this->curl,
//        CURLOPT_HTTPHEADER,
//        $headers
//    );
//
//    $result = curl_exec($this->curl);
//    $header = curl_getinfo($this->curl);
//
//    $data = json_decode($result, true);
//    $status = $header['http_code'];
//
//    if ($status == '200') {
//        if (is_array($data) && isset($data['message'])) {
//            return $data['message'];
//        } else {
//            return $data;
//        }
//    } else if ($status == '204') {
//        return null;
//    } else {
//        @ob_end_clean();
//        echo '<pre>';
//        echo 'Status<br/>';
//        print_r(array(
//                    'url' => $this->url,
//                    'status' => $status,
//                    'method' => $function,
//                    'verb' => $verb,
//                    'token' => $token,
//                    'params' => $parameters,
//                    'data' => $data
//                ));
//        echo 'CURL Error<br/>';
//        print_r(curl_error($this->curl));
//        echo '</pre>';
//        die();
//    }
//}
//}
//
//
//<?php
//
//declare(strict_types=1);
