<?php

namespace bamboo\app\business\api;

use bamboo\core\base\CConfig;

abstract class tradingApiCallAbstract extends apiCallAbstract
{
    /**
     * @var array
     */
    protected $params;
    /**
     * @var string
     */
    protected $token;
    /**
     * @var CConfig
     */
    protected $config;
    /**
     * @var string
     */
    protected $country;

    /**
     * @param CConfig $config
     */
    protected function setConfig(CConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @param array $params
     * @param $token
     * @param $country
     */
    public function pass(array $params, $token, $country)
    {
        $this->country = $country;
        $auth = ['RequesterCredentials'=>['eBayAuthToken'=>$token]];
        $default = ['ErrorLanguage'=>$this->config['error_language'],'WarningLevel'=>$this->config['warning_level']];
        $this->params = array_merge($auth,$params,$default);
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $request = $this->array_to_xml($this->params, $this->xml);

        $xml = new \DOMDocument();
        $xml->loadXML($request, LIBXML_NOBLANKS);
        if (!$xml->schemaValidate($this->config['trading']['schema'])) {
            throw new \InvalidArgumentException('Invalid XML request');
        }

        $headers = [
            'X-EBAY-API-COMPATIBILITY-LEVEL: '.$this->config['trading']['version'],
            'X-EBAY-API-SESSION-CERTIFICATE: '.$this->config['devid'].';'.$this->config['appid'].';'.$this->config['certid'],
            'X-EBAY-API-DEV-NAME: '.$this->config['devid'],
            'X-EBAY-API-APP-NAME: '.$this->config['appid'],
            'X-EBAY-API-CERT-NAME: '.$this->config['certid'],
            'X-EBAY-API-CALL-NAME: '.$this->config['iam'],
            'X-EBAY-API-SITEID: '.((is_null($this->country)) ? $this->config['default_site'] : $this->country),
            'X-EBAY-API-DETAIL-LEVEL: '.$this->config['detail_level'],
            'Content-Type: text/xml',
            'Content-Length: '.strlen($request)
        ];

        $curl = curl_init($this->config['trading']['endpoint']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request);

        return curl_exec($curl);
    }
}