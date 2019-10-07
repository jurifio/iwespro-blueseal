<?php

namespace bamboo\controllers\api\classes;

use bamboo\controllers\api\AJWTManager;
use bamboo\core\base\CConfig;
use bamboo\core\base\CFTPClient;
use bamboo\core\exceptions\BambooConfigException;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooLogicException;
use bamboo\core\exceptions\BambooOutOfBoundException;
use bamboo\core\utils\amazonPhotoManager\ImageEditor;
use bamboo\core\utils\slugify\CSlugify;
use bamboo\domain\entities\CDirtyProduct;
use bamboo\domain\entities\COrderLine;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductNameTranslation;
use bamboo\domain\repositories\CProductNameTranslationRepo;
use bamboo\utils\time\STimeToolbox;


/**
 * Class products
 * @package bamboo\controllers\api
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 20/07/2018
 * @since 1.0
 */
class products extends AApi
{

    private $shop;
    private $uniqueId;
    private $generalSettings;
    private $specSettings;

    /**
     * products constructor.
     * @param $app
     * @param $data
     * @throws BambooConfigException
     * @throws BambooException
     * @throws \bamboo\core\exceptions\RedPandaCookieException
     */
    public function __construct($app,$data)
    {
        parent::__construct($app,$data);
        $this->readEntitySettings();
        $this->shop = \Monkey::app()->repoFactory->create('SiteApi')->findOneBy(['id' => $this->id]);
        $this->uniqueId = uniqid();
    }

    public function createAction($action)
    {
        if (!is_null($this->auth)) {
            return $this->auth;
        }
        return $this->{$action}();
    }

    public function get()
    {
    }


    /**
     * @return array|bool|string
     * @throws BambooLogicException
     * @throws BambooOutOfBoundException
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function post()
    {

        $this->specSettings = $this->generalSettings->fetchAll('post');

        if ($this->checkIntervalForNextCall('POST','Documents',$this->specSettings['intervalSecondForNextCall'])) {
            $res = $this->validateFile();
            if ($res === true) {
                $this->processFile();
                $this->workDirtyData();
                $zipFile = $this->saveFile();
                if ($zipFile !== true) unlink($zipFile);
                $this->report($this::POST,'Products','success','Product inserted correctly',null,$this->uniqueId,$this->id);
                return true;
            }
        } else $res = 'Tempo necessario fra due esportazioni di prodotto: ' . STimeToolbox::formatTo('seconds','hours',$this->specSettings['intervalSecondForNextCall']) . ' ore';

        return $res;
    }


    /**
     * @return array|bool|string
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function put()
    {
        $this->specSettings = $this->generalSettings->fetchAll('put');
        $res = $this->validateFile(1);
        if ($res === true) {
            $res = $this->updateProduct();
            if ($res === true) return true;
        }

        return $res;
    }

    public function delete()
    {
    }

    /**
     * @throws BambooConfigException
     */
    private function readEntitySettings()
    {

        $filePath = \Monkey::app()->rootPath() . \Monkey::app()->cfg()->fetch("paths","api") . 'documents.json';

        if (!file_exists($filePath)) throw new BambooConfigException('Configuration not found for Importer: ' . $filePath);

        $this->generalSettings = new CConfig($filePath);
        $this->generalSettings->load();

        return true;
    }

    /**
     * @param null $maxProduct
     * @return array|bool|string
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    private function validateFile($maxProduct = null)
    {
        $res = null;
        $requiredFields = $this->specSettings['requiredFields'];
        $notRequiredFields = $this->specSettings['notRequiredFields'];
        $totalFields = count($requiredFields) + count($notRequiredFields);

        if (!is_null($maxProduct)) {
            if (count($this->data['json']) > $maxProduct) return 'Puoi specificare al massimo ' . $maxProduct . ' prodotti';
        }

        foreach ($this->data['json'] as $product) {
            if (count($product) != $totalFields) return 'Hai specificato ' . count($product) . ' campi su ' . $totalFields;
            $notValidFields = [];

            foreach ($product as $field => $value) {
                if (
                $this->checkFieldType($requiredFields + $notRequiredFields,$field,$value)
                ) {
                    continue;
                };

                $notValidFields[][$field] = 'Invalid field or type';
            }

            if (!empty($notValidFields)) return $notValidFields;
        }

        $this->report($this::POST,'Products','report','File validated correctly',null,$this->uniqueId,$this->id);
        return true;
    }

    private function checkFieldType($fields,$field,$value)
    {

        if (array_key_exists($field,$this->specSettings['requiredFields'])) {
            $mandatory = true;
        } else if (array_key_exists($field,$this->specSettings['notRequiredFields'])) {
            $mandatory = false;
        } else return false;

        $type = $fields[$field];

        $resType = null;
        switch ($type) {
            case 'string':
                if ($mandatory) {
                    $resType = is_string($value) && !empty(trim($value));
                } else {
                    $resType = is_string($value);
                }
                break;
            case 'numeric':
                $resType = is_numeric(str_replace(',','.',$value));
                break;
            case 'string || numeric':
                if ($mandatory) {
                    if ((is_string($value) && !empty(trim($value))) || is_numeric(str_replace(',','.',$value))) {
                        $resType = true;
                    } else {
                        $resType = false;
                    }
                } else {
                    if (is_string($value) || is_numeric(str_replace(',','.',$value))) {
                        $resType = true;
                    } else {
                        $resType = false;
                    }
                }
                break;
            case 'array':
                if ($mandatory) {
                    $resType = is_array($value) && !empty($value);
                } else {
                    $resType = is_array($value);
                }
                break;
        }

        return $resType;
    }

    /**
     * @throws BambooLogicException
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    private function processFile()
    {
        $this->report($this::POST,'Documents','report','Generate documents','Init insert of: ' . count($this->data['json']) . ' elements',$this->uniqueId,$this->id);
        foreach ($this->data['json'] as $order) {
            $orderId = $order->remoteIwesOrderId;
            $remoteShopSupplierId = $order->shop;
            $remoteShopSellerId = $order->remoteSellerid;
            $isParallel = $order->isParallel;
            $remoteOrderSupplierId=$order->remoteOrderSupplierId;
            $order=\Monkey::app()->repoFactory->create('Order')->findOneBy(['id'=>$orderId]);
            $OrderLine=\Monkey::app()->repoFactory->create('OrderLine')->findBy(['orderId']);

        }
    }

    private function processInvoiceToSeller (COrderLine $orderLine,int $orderId, int $remoteOrderSupplierId,int $remoteShopSellerId, float $amountForInvoice){


    }

    private function processInvoiceToSupplier($orderId =null){

    }



}