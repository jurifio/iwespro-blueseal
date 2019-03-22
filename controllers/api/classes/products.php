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
    public function __construct($app, $data)
    {
        parent::__construct($app, $data);
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

        if ($this->checkIntervalForNextCall('POST', 'Products', $this->specSettings['intervalSecondForNextCall'])) {
            $res = $this->validateFile();
            if ($res === true) {
                $this->processFile();
                $this->workDirtyData();
                $zipFile = $this->saveFile();
                if($zipFile !== true) unlink($zipFile);
                $this->report($this::POST, 'Products', 'success', 'Product inserted correctly', null, $this->uniqueId, $this->id);
                return true;
            }
        } else $res = 'Tempo necessario fra due esportazioni di prodotto: ' . STimeToolbox::formatTo('seconds', 'hours', $this->specSettings['intervalSecondForNextCall']) . ' ore';

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
            if($res === true) return true;
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

        $filePath = \Monkey::app()->rootPath() . \Monkey::app()->cfg()->fetch("paths", "api") . 'products.json';

        if (!file_exists($filePath)) throw new BambooConfigException('Configuration not found for Importer: ' . $filePath);

        $this->generalSettings = new CConfig($filePath);
        $this->generalSettings->load();

        return true;
    }

    /**
     * @param null $maxProduct
     * @return array|bool|string
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
            if (count($product) != $totalFields) return 'Hai specificato ' . count($product) . ' su ' . $totalFields;
            $notValidFields = [];

            foreach ($product as $field => $value) {
                if (
                    array_key_exists($field, $requiredFields) && $this->checkFieldType($requiredFields, $field, $value)
                    || array_key_exists($field, $notRequiredFields) && $this->checkFieldType($notRequiredFields, $field, $value)
                ) {
                    continue;
                };

                $notValidFields[][$field] = 'Invalid field or type';
            }

            if (!empty($notValidFields)) return $notValidFields;
        }

        $this->report($this::POST, 'Products', 'report', 'File validated correctly', null, $this->uniqueId, $this->id);
        return true;
    }

    private function checkFieldType($fields, $field, $value)
    {
        $type = $fields[$field];
        $resType = null;
        switch ($type) {
            case 'string':
                $resType = is_string($value);
                break;
            case 'numeric':
                $resType = is_numeric(str_replace(',', '.', $value));
                break;
            case 'string || numeric':
                if (is_string($value) || is_numeric(str_replace(',', '.', $value))) {
                    $resType = true;
                } else {
                    $resType = false;
                }
                break;
            case 'array':
                $resType = is_array($value);
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
        $countNewDirtyProduct = 0;
        $countUpdatedDirtyProduct = 0;
        $countNewDirtySku = 0;
        $countUpdatedDirtySku = 0;
        $seenSkus = [];

        $this->report($this::POST, 'Products', 'report', 'Process DirtyProduct', 'Init insert of: ' . count($this->data['json']) . ' elements', $this->uniqueId, $this->id);

        foreach ($this->data['json'] as $product) {

            $newDirtyProduct = [];
            $newDirtyProductExtend = [];
            $newDirtySku = [];

            //DIRTY PRODUCT
            try {
                \Monkey::app()->repoFactory->beginTransaction();

                $newDirtyProduct['shopId'] = $this->shop->shopId;
                $newDirtyProduct['extId'] = $product['referenceId'];
                $newDirtyProduct['brand'] = $product['brand'];
                $newDirtyProduct['itemno'] = $product['supplierArticle'];
                $newDirtyProduct['value'] = floatval(str_replace(',', '.', $product['supplierPrice']));
                $newDirtyProduct['price'] = floatval(str_replace(',', '.', $product['marketPrice']));
                $newDirtyProduct['var'] = $product['var'];
                $newDirtyProduct['text'] = implode(',', $newDirtyProduct);

                $newDirtyProduct['checksum'] = md5(implode(',', $newDirtyProduct));
                $newDirtyProduct['dirtyStatus'] = 'F';


                $newDirtyProductExtend['name'] = $product['name'] ?: null;
                $newDirtyProductExtend['description'] = $product['description'] ?: null;
                $newDirtyProductExtend['season'] = $product['season'];
                $newDirtyProductExtend['audience'] = $product['audience'] ?: null;
                $newDirtyProductExtend['generalColor'] = $product['var'];
                $newDirtyProductExtend['colorDescription'] = $product['colorDescription'] ?: null;
                $newDirtyProductExtend['sizeGroup'] = $product['sizeGroup'] ?: null;

                for ($i = 0; $i < 5; $i++) {
                    if (!isset($product['categories'][$i])) break;
                    $newDirtyProductExtend['cat' . ($i + 1)] = $product['categories'][$i];
                }

                for ($i = 0; $i < 3; $i++) {
                    if (!isset($product['tags'][$i])) break;
                    $newDirtyProductExtend['tag' . ($i + 1)] = $product['tags'][$i];
                }

                $existingDirtyProduct = \Monkey::app()->dbAdapter->selectCount('DirtyProduct', ['checksum' => $newDirtyProduct['checksum']]);

                $mainKey = [];
                if ($existingDirtyProduct == 0) {
                    //se non esiste lo cerco con l'articolo

                    $mainKey['itemno'] = $newDirtyProduct['itemno'];
                    $mainKey['var'] = $newDirtyProduct['var'];
                    $mainKey['shopId'] = $this->shop->shopId;

                    $existProductWithMainKey = \Monkey::app()->dbAdapter->select('DirtyProduct', $mainKey)->fetch();

                    //lo trovo --> qualcosa è cambiato presumibilmente il value o il price
                    if ($existProductWithMainKey) {
                        \Monkey::app()->dbAdapter->update('DirtyProduct', [
                            'value' => $newDirtyProduct['value'],
                            'price' => $newDirtyProduct['price'],
                            'text' => $newDirtyProduct['text'],
                            'checksum' => $newDirtyProduct['checksum']
                        ], $mainKey);

                        $countUpdatedDirtyProduct++;

                        $dirtyProductId = $existProductWithMainKey['id'];

                        //aggiorno DirtyProductExtend
                        $existingDirtyProductExtend = \Monkey::app()->dbAdapter->select('DirtyProductExtend', ['dirtyProductId' => $existProductWithMainKey['id']])->fetch();

                        if ($existingDirtyProductExtend) {
                            \Monkey::app()->dbAdapter->update('DirtyProductExtend', $newDirtyProductExtend, ['dirtyProductId' => $existProductWithMainKey['id']]);
                        } else {
                            $this->report($this::POST, 'Products', 'error', 'DirtyProductExtend', 'Error while looking at dirtyProductId: ' . $existProductWithMainKey['id'] . ' on DirtyProductExtend table', $this->uniqueId, $this->id);
                        }
                    } else {

                        //inserisco il prodotto
                        $newDirtyProductExtend['dirtyProductId'] = \Monkey::app()->dbAdapter->insert('DirtyProduct', $newDirtyProduct);

                        //inserisco dirty product extend
                        $newDirtyProductExtend['shopId'] = $this->shop->shopId;

                        \Monkey::app()->dbAdapter->insert('DirtyProductExtend', $newDirtyProductExtend);
                        $countNewDirtyProduct++;

                        $dirtyProductId = $newDirtyProductExtend['dirtyProductId'];
                    }
                } else if ($existingDirtyProduct > 1) {
                    $this->report($this::POST, 'Products', 'report', 'Multiple dirty product founded', 'Procedure has founded ' . $existingDirtyProduct . ' dirty product', $this->uniqueId, $this->id);
                    continue;
                } else {
                    $dirtyProductId = \Monkey::app()->dbAdapter->select('DirtyProduct', ['checksum' => $newDirtyProduct['checksum']])->fetch()['id'];
                }

                $dirtyPhotos = \Monkey::app()->dbAdapter->select('DirtyPhoto', ['dirtyProductId' => $dirtyProductId])->fetchAll();
                $position = 0;

                foreach ($product['imgs'] as $img) {
                    if (empty(trim($img))) continue;
                    foreach ($dirtyPhotos as $exImg) {
                        if ($exImg['url'] == $img) continue 2;
                    }
                    $position++;
                    \Monkey::app()->dbAdapter->insert('DirtyPhoto', [
                        'dirtyProductId' => $dirtyProductId,
                        'shopId' => $this->shop->shopId,
                        'url' => $img,
                        'location' => 'url',
                        'position' => $position,
                        'worked' => 0
                    ]);
                }

                \Monkey::app()->repoFactory->commit();
            } catch (\Throwable $e) {
                \Monkey::app()->repoFactory->rollback();
                $this->report($this::POST, 'Products', 'error', 'Error reading Product', 'Error reading Product: ' . json_encode($product) . ' Error detail: ' . $e->getMessage(), $this->uniqueId, $this->id);
                continue;
            }

            //DIRTY SKU
            try {
                $this->report($this::POST, 'Products', 'report', 'Process DirtySku', 'Init insert sku', $this->uniqueId, $this->id);

                $dirtySku = [];
                $mainKeyForSku = [];
                $mainKeyForSku['itemno'] = $product['supplierArticle'];
                $mainKeyForSku['var'] = $product['var'];
                $mainKeyForSku['shopId'] = $this->shop->shopId;

                $dirtyProduct = $existingDirtyProductExtend = \Monkey::app()->dbAdapter->select('DirtyProduct', $mainKeyForSku)->fetch();

                if (!$dirtyProduct) {
                    $this->report($this::POST, 'Products', 'error', 'Reading Skus', 'Dirty Product not found while looking at sku. Product: ' . json_encode($product), $this->uniqueId, $this->id);
                    continue;
                }


                $newDirtySku['size'] = $product['size'];
                $newDirtySku['shopId'] = $this->shop->shopId;
                $newDirtySku['dirtyProductId'] = $dirtyProduct['id'];
                $newDirtySku['value'] = floatval(str_replace(',', '.', $product['supplierPrice']));
                $newDirtySku['price'] = floatval(str_replace(',', '.', $product['marketPrice']));
                $newDirtySku['qty'] = $product['qty'];
                $newDirtySku['barcode'] = $product['ean'] ?: null;
                $newDirtySku['barcode_int'] = $product['barcodeInt'] ?: null;
                $newDirtySku['text'] = implode(',', $newDirtySku);
                $newDirtySku['checksum'] = md5(implode(',', $newDirtySku));

                //cerco lo sku con il checksum
                $existDirtySku = \Monkey::app()->dbAdapter->selectCount('DirtySku', ['checksum' => $newDirtySku['checksum']]);

                if ($existDirtySku == 0) {

                    $existDirtySkuWithMainKey = \Monkey::app()->dbAdapter->select('DirtySku', [
                        'dirtyProductId' => $newDirtySku['dirtyProductId'],
                        'shopId' => $newDirtySku['shopId'],
                        'size' => $newDirtySku['size']
                    ])->fetch();

                    if ($existDirtySkuWithMainKey) {
                        //update
                        \Monkey::app()->dbAdapter->update('DirtySku', [
                            'value' => $newDirtySku['value'],
                            'price' => $newDirtySku['price'],
                            'qty' => $newDirtySku['qty'],
                            'changed' => 1,
                            'text' => $newDirtySku['text'],
                            'checksum' => $newDirtySku['checksum']
                        ], [
                            'dirtyProductId' => $existDirtySkuWithMainKey['dirtyProductId'],
                            'shopId' => $existDirtySkuWithMainKey['shopId'],
                            'size' => $existDirtySkuWithMainKey['size']
                        ]);

                        $dirtySku['id'] = $existDirtySkuWithMainKey['id'];
                        $seenSkus[] = $dirtySku['id'];
                        $countUpdatedDirtySku++;
                    } else {
                        //INSERT
                        $dirtySku['id'] = \Monkey::app()->dbAdapter->insert('DirtySku', $newDirtySku);
                        $seenSkus[] = $dirtySku['id'];
                        $countNewDirtySku++;
                    }

                } else if ($existDirtySku > 1) {
                    $this->report($this::POST, 'Products', 'error', 'Multiple dirty sku founded', 'Procedure has founded ' . $existDirtySku . ' dirty sku', $this->uniqueId, $this->id);
                    continue;
                } else if ($existDirtySku == 1) {
                    $noChangedSku = \Monkey::app()->dbAdapter->select('DirtySku', ['checksum' => $newDirtySku['checksum']])->fetch();
                    $seenSkus[] = $noChangedSku['id'];
                }


            } catch (\Throwable $e) {
                $this->report($this::POST, 'Products', 'error', 'Error reading sku', 'Error reading sku: ' . json_encode($product) . ' Error detail: ' . $e->getMessage(), $this->uniqueId, $this->id);
                continue;
            }

        }

        $this->report($this::POST, 'Products', 'report', 'End products', 'End of reading and writing dirty product: New Dirty Product: ' . $countNewDirtyProduct . ' Updated Dirty product: ' . $countUpdatedDirtyProduct, $this->uniqueId, $this->id);
        $this->report($this::POST, 'Products', 'report', 'End skus', 'End of reading and writing dirty skus: New Dirty Sku: ' . $countNewDirtySku . ' Updated Dirty product: ' . $countUpdatedDirtySku, $this->uniqueId, $this->id);

        $this->findZeroSkus($seenSkus);
    }

    /**
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    private function updateProduct()
    {

        $prToUpdate = $this->data['json'][0];

        $this->shop = \Monkey::app()->repoFactory->create('SiteApi')->findOneBy(['id' => $this->id]);
        $this->uniqueId = uniqid();
        $dirtyProduct = null;
        $dirtySku = null;


        try {
            $mainKey = [];
            $mainKey['shopId'] = $this->shop->shopId;
            $mainKey['extId'] = $this->data['resource'];

            $dirtyProduct = \Monkey::app()->dbAdapter->select('DirtyProduct', $mainKey)->fetch();

            if(!$dirtyProduct) return 'Il prodotto che stai cercando di aggiornare non esiste';

            $this->report($this::PUT, 'Products', 'report', 'Init updating product', 'Init updating product: ' . $dirtyProduct['id'] . ' with size ' . $prToUpdate['size'], $this->uniqueId, $this->id);

            $dirtySku = \Monkey::app()->dbAdapter->select('DirtySku', [
                'dirtyProductId' => $dirtyProduct['id'],
                'shopId' => $this->shop->shopId,
                'size' => $prToUpdate['size']
            ])->fetch();

            if(!$dirtySku){
                throw new BambooException('Product founded, sku NOT founded');
            }

            $updDirtySku['size'] = $dirtySku['size'];
            $updDirtySku['shopId'] = $dirtySku['shopId'];
            $updDirtySku['dirtyProductId'] = $dirtySku['dirtyProductId'];
            $updDirtySku['value'] = floatval(str_replace(',', '.', $dirtySku['value']));
            $updDirtySku['price'] = floatval(str_replace(',', '.', $dirtySku['price']));
            $updDirtySku['qty'] = $prToUpdate['qty'];
            $updDirtySku['barcode'] = $dirtySku['barcode'];
            $updDirtySku['barcode_int'] = $dirtySku['barcode_int'];
            $updDirtySku['text'] = implode(',', $updDirtySku);
            $updDirtySku['checksum'] = md5(implode(',', $updDirtySku));

            \Monkey::app()->dbAdapter->update('DirtySku', [
                'qty' => $updDirtySku['qty'],
                'changed' => 1,
                'text' => $updDirtySku['text'],
                'checksum' => $updDirtySku['checksum']
            ], [
                'dirtyProductId' => $dirtyProduct['id'],
                'shopId' => $this->shop->shopId,
                'size' => $prToUpdate['size']
            ]);
            $this->report($this::PUT, 'Products', 'report', 'End updating product', 'End updating product: ' . $dirtyProduct['id'] . ' with size ' . $prToUpdate['size'], $this->uniqueId, $this->id);

        } catch (\Throwable $e) {
            $this->report($this::PUT, 'Products', 'report', 'Error updating product', 'Error updating product: ' . $dirtyProduct['id'] . ' with size ' . $prToUpdate['size'] . 'Error detail: ' . $e->getMessage(), $this->uniqueId, $this->id);
            return false;
        }

        return true;
    }

    /**
     * @param $seenSkus
     * @throws BambooLogicException
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    private function findZeroSkus($seenSkus)
    {
        if (count($seenSkus) == 0) {
            throw new BambooLogicException('seenSkus contains 0 elements');
        }
        $res = \Monkey::app()->dbAdapter->query("SELECT ds.id
                                                      FROM DirtySku ds 
                                                        JOIN DirtyProduct dp ON ds.dirtyProductId = dp.id
                                                        JOIN ProductSku ps ON ps.productId = dp.productId AND
                                                          ps.productVariantId = dp.productVariantId AND
                                                          ps.shopId = ds.shopId AND
                                                          ds.productSizeId = ps.productSizeId
                                                      WHERE
                                                          dp.fullMatch = 1 AND
                                                          ds.qty != 0 AND
                                                          ps.shopId = ?", [$this->shop->shopId])->fetchAll();

        $this->report($this::POST, 'Products', 'error', 'Seen skus', "Seen Skus: " . count($seenSkus), $this->uniqueId, $this->id);
        $this->report($this::POST, 'Products', 'error', 'Seen skus', "Product not at 0: " . count($res), $this->uniqueId, $this->id);

        $i = 0;

        foreach ($res as $one) {
            if (!in_array($one['id'], $seenSkus)) {
                $qty = \Monkey::app()->dbAdapter->update("DirtySku", ["qty" => 0, "changed" => 1, "checksum" => null], $one);
                $i++;
            }
        }
        $this->report($this::POST, 'Products', 'error', 'Seen skus', "Product set 0: " . $i, $this->uniqueId, $this->id);
    }


    /**
     * @param null $args
     * @throws BambooOutOfBoundException
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    private function workDirtyData($args = null)
    {
        $this->report($this::POST, 'Products', 'report', 'updateDictionaries launch', null, $this->uniqueId, $this->id);
        $this->updateDictionaries();
        $this->report($this::POST, 'Products', 'report', 'updateDictionaries end', null, $this->uniqueId, $this->id);

        $this->report($this::POST, 'Products', 'report', 'createProducts launch', null, $this->uniqueId, $this->id);
        $this->createProducts();
        $this->report($this::POST, 'Products', 'report', 'createProducts end', null, $this->uniqueId, $this->id);

        $this->report($this::POST, 'Products', 'report', 'checkForNewDetails launch', null, $this->uniqueId, $this->id);
        $this->checkForNewDetails();
        $this->report($this::POST, 'Products', 'report', 'checkForNewDetails end', null, $this->uniqueId, $this->id);

        $this->report($this::POST, 'Products', 'report', 'sendPhotos launch', null, $this->uniqueId, $this->id);
        $this->sendPhotos();
        $this->report($this::POST, 'Products', 'report', 'sendPhotos end', null, $this->uniqueId, $this->id);
    }

    /**
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function updateDictionaries()
    {
        $i = $this->updateBrandDictionary();
        $this->report($this::POST, 'Products', 'report', 'updateDictionaries', 'Brand terms inserted: ' . $i, $this->uniqueId, $this->id);

        $i = $this->updateSeasonDictionary();
        $this->report($this::POST, 'Products', 'report', 'updateDictionaries', 'Season terms inserted: ' . $i, $this->uniqueId, $this->id);

        $i = $this->updateCategoryDictionary();
        $this->report($this::POST, 'Products', 'report', 'updateDictionaries', 'Category terms inserted: ' . $i, $this->uniqueId, $this->id);

        $i = $this->updateTagDictionary();
        $this->report($this::POST, 'Products', 'report', 'updateDictionaries', 'Tag terms inserted: ' . $i, $this->uniqueId, $this->id);

        $i = $this->updateGereralColorDictionary();
        $this->report($this::POST, 'Products', 'report', 'updateDictionaries', 'Color terms inserted: ' . $i, $this->uniqueId, $this->id);

        $i = $this->updateSizeDictionary();
        $this->report($this::POST, 'Products', 'report', 'updateDictionaries', 'Size terms inserted: ' . $i, $this->uniqueId, $this->id);

        $i = $this->updateDetailDictionary();
        $this->report($this::POST, 'Products', 'report', 'updateDictionaries', 'Detail terms inserted: ' . $i, $this->uniqueId, $this->id);

    }

    /**
     * @return int
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    protected function updateBrandDictionary()
    {
        \Monkey::app()->dbAdapter->query("INSERT IGNORE INTO DictionaryBrand (shopId, term)
										SELECT DISTINCT shopId, brand
										FROM DirtyProduct
										WHERE shopId = ? AND dirtyStatus != 'C'", [$this->shop->shopId]);

        return \Monkey::app()->dbAdapter->countAffectedRows();
    }

    /**
     * @return int
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    protected function updateSeasonDictionary()
    {
        \Monkey::app()->dbAdapter->query("INSERT IGNORE INTO DictionarySeason (shopId, term) 
                                        SELECT DISTINCT dpe.shopId, dpe.season 
                                        FROM DirtyProductExtend  dpe, DirtyProduct dp 
                                        WHERE dpe.dirtyProductId = dp.id AND dpe.shopId = ? AND dp.dirtyStatus != 'C'", [$this->shop->shopId]);

        return \Monkey::app()->dbAdapter->countAffectedRows();
    }

    /**
     * @return int
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    protected function updateCategoryDictionary()
    {
        \Monkey::app()->dbAdapter->query("INSERT IGNORE INTO DictionaryCategory (shopId, term) 
                                        SELECT DISTINCT dpe.shopId, concat(ifnull(audience,''),'-',ifnull(cat1,''),'-',ifnull(cat2,''),'-',ifnull(cat3,''),'-',ifnull(cat4,''),'-',ifnull(cat5,'')) 
                                        FROM DirtyProductExtend  dpe, DirtyProduct dp 
                                        WHERE dpe.dirtyProductId = dp.id AND dpe.shopId = ? AND dp.dirtyStatus != 'C'", [$this->shop->shopId]);

        return \Monkey::app()->dbAdapter->countAffectedRows();
    }

    /**
     * @return int
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    protected function updateTagDictionary()
    {
        \Monkey::app()->dbAdapter->query("INSERT IGNORE INTO DictionaryTag (shopId, term) 
                                        SELECT DISTINCT dpe.shopId, dpe.tag1 
                                        FROM DirtyProductExtend  dpe, DirtyProduct dp 
                                        WHERE dpe.dirtyProductId = dp.id AND dpe.shopId = ? AND dp.dirtyStatus != 'C' AND trim(tag1) != ''", [$this->shop->shopId]);
        $i = \Monkey::app()->dbAdapter->countAffectedRows();
        \Monkey::app()->dbAdapter->query("INSERT IGNORE INTO DictionaryTag (shopId, term) 
                                        SELECT DISTINCT dpe.shopId, dpe.tag2 
                                        FROM DirtyProductExtend  dpe, DirtyProduct dp 
                                        WHERE dpe.dirtyProductId = dp.id AND dpe.shopId = ? AND dp.dirtyStatus != 'C' AND trim(tag2) != ''", [$this->shop->shopId]);
        $i += \Monkey::app()->dbAdapter->countAffectedRows();
        \Monkey::app()->dbAdapter->query("INSERT IGNORE INTO DictionaryTag (shopId, term) 
                                        SELECT DISTINCT dpe.shopId, dpe.tag3 
                                        FROM DirtyProductExtend  dpe, DirtyProduct dp 
                                        WHERE dpe.dirtyProductId = dp.id AND dpe.shopId = ? AND dp.dirtyStatus != 'C' AND trim(tag3) != ''", [$this->shop->shopId]);
        $i += \Monkey::app()->dbAdapter->countAffectedRows();

        return $i;
    }

    /**
     * @return int
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    protected function updateGereralColorDictionary()
    {
        \Monkey::app()->dbAdapter->query("INSERT IGNORE INTO DictionaryColorGroup (shopId, term) 
                                        SELECT DISTINCT dpe.shopId, generalColor 
                                        FROM DirtyProductExtend  dpe, DirtyProduct dp 
                                        WHERE dpe.dirtyProductId = dp.id AND dpe.shopId = ? AND dp.dirtyStatus != 'C'", [$this->shop->shopId]);

        return \Monkey::app()->dbAdapter->countAffectedRows();
    }

    /**
     * @return int
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    protected function updateSizeDictionary()
    {
        \Monkey::app()->dbAdapter->query("INSERT  INTO DictionarySize (shopId, term, categoryFriend) 
                                        SELECT DISTINCT ds.shopId, size , concat(ifnull(audience,''),'-',ifnull(cat1,''),'-',ifnull(cat2,''),'-',ifnull(cat3,''),'-',ifnull(cat4,''),'-',ifnull(cat5,''))
                                        FROM DirtySku ds, DirtyProduct dp JOIN 
                                        DirtyProductExtend dpe ON dp.id= dpe.dirtyProductId
                                        WHERE dp.id = ds.dirtyProductId AND ds.shopId = ? AND trim(size) != '' AND dp.dirtyStatus != 'C'
                                        ON DUPLICATE KEY UPDATE 
                                        DictionarySize.shopId=ds.shopId,
                                        DictionarySize.term=size,
                                        DictionarySize.categoryFriend=concat(ifnull(audience,''),'-',ifnull(cat1,''),'-',ifnull(cat2,''),'-',ifnull(cat3,''),'-',ifnull(cat4,''),'-',ifnull(cat5,''))", [$this->shop->shopId]);

        return \Monkey::app()->dbAdapter->countAffectedRows();
    }

    /**
     * @return int
     */
    protected function updateDetailDictionary()
    {
        /*\Monkey::app()->dbAdapter->query("INSERT IGNORE INTO DictionaryDetail (shopId, term)
                                        SELECT DISTINCT dp.shopId, content 
                                        FROM DirtyDetail dd, DirtyProduct dp 
                                        WHERE dd.dirtyProductId = dp.id AND dp.shopId = ? AND dp.dirtyStatus != 'C'", [$this->shop->shopId]);

        return \Monkey::app()->dbAdapter->countAffectedRows();
        */
        return 0;
    }

    /**
     * @return int
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function createProducts()
    {
        /** fetch dictionaries */
        try {
            $brandDic = $this->mapDictionary("Brand", \Monkey::app()->dbAdapter->query("SELECT term, productBrandId AS foreignKey FROM DictionaryBrand WHERE shopId = ? AND productBrandId IS NOT NULL", [$this->shop->shopId])->fetchAll());
            $categoryDic = $this->mapDictionary("Category", \Monkey::app()->dbAdapter->query("SELECT term, productCategoryId AS foreignKey FROM DictionaryCategory WHERE shopId = ? AND productCategoryId IS NOT NULL", [$this->shop->shopId])->fetchAll());
            $colorGroupDic = $this->mapDictionary("ColorGroup", \Monkey::app()->dbAdapter->query("SELECT term, productColorGroupId AS foreignKey FROM DictionaryColorGroup WHERE shopId = ? AND productColorGroupId IS NOT NULL", [$this->shop->shopId])->fetchAll());
            $seasonDic = $this->mapDictionary("Season", \Monkey::app()->dbAdapter->query("SELECT term, productSeasonId AS foreignKey FROM DictionarySeason WHERE shopId = ? AND productSeasonId IS NOT NULL", [$this->shop->shopId])->fetchAll());

            $detailSet = $this->mapDictionary("Detail", \Monkey::app()->dbAdapter->query("SELECT slug AS term, id AS foreignKey FROM ProductDetail WHERE slug != ''", [])->fetchAll());

            $sizeConnector = \Monkey::app()->repoFactory->create('ImporterConnector')->em()->findBySql("SELECT id FROM ImporterConnector WHERE shopId = ? AND scope = ?", [$this->shop->shopId, 'sizeGroupId']);
            if ($sizeConnector->isEmpty()) throw new BambooOutOfBoundException('Could not find connector for sizes');
            /** @var \bamboo\domain\entities\CImporterConnector $sizeConnector */
            $sizeConnector = $sizeConnector->getFirst();

            //$productSheetConnector = \Monkey::app()->repoFactory->create('ImporterConnector')->em()->findBySql("SELECT id FROM ImporterConnector where shopId = ? and scope = ?",[$this->shop->shopId,'sheetName']);
            //if($productSheetConnector->isEmpty()) throw new BambooOutOfBoundException('Could not find connector for sizes');
            //$productSheetConnector = $productSheetConnector->getFirst();
            try {
                $tagDic = $this->mapDictionary("Tag", \Monkey::app()->dbAdapter->query("SELECT term, tagId AS foreignKey FROM DictionaryTag WHERE shopId = ? AND tagId IS NOT NULL", [$this->shop->shopId])->fetchAll());
            } catch (BambooOutOfBoundException $e) {
            }

        } catch (BambooOutOfBoundException $e) {
            $this->report($this::POST, 'Products', 'report', 'Create Products', 'Found emptyDictionary for: ' . $e->getMessage(), $this->uniqueId, $this->id);
            return false;
        }

        $dictionaryProblem = false;
        /** fetch empty dirtyProduct */
        $dps = \Monkey::app()->dbAdapter->query("	SELECT dp.id
												FROM DirtyProduct dp JOIN DirtySku ds ON dp.id = ds.dirtyProductId
												WHERE dp.shopId = ? AND
													  productId IS NULL AND
													  productVariantId IS NULL AND
													  dirtyStatus = 'F' GROUP BY dp.id HAVING sum(ds.qty) > 0", [$this->shop->shopId])->fetchAll();

        $dpEm = \Monkey::app()->repoFactory->create('DirtyProduct');
        $productFactory = \Monkey::app()->repoFactory->create('Product');
        /** @var CProductNameTranslationRepo $nameFactory */
        $nameFactory = \Monkey::app()->repoFactory->create('ProductNameTranslation');
        $descriptionFactory = \Monkey::app()->repoFactory->create('ProductDescriptionTranslation');
        $variantFactory = \Monkey::app()->repoFactory->create('ProductVariant');
        $shopProduct = \Monkey::app()->repoFactory->create('ShopHasProduct');
        $done = 0;

        $sheetPrototype = \Monkey::app()->repoFactory->create('ProductSheetPrototype')->findOneBy(["name" => "Generica"]);

        $slugify = new CSlugify();
        $this->report($this::POST, 'Products', 'report', 'Create Products', 'working ' . count($dps) . ' dirtyProducts', $this->uniqueId, $this->id);

        foreach ($dps as $dpId) {
            $this->report($this::POST, 'Products', 'report', 'Create Products', 'working for ' . $dpId['id'], $this->uniqueId, $this->id);

            try {
                /** @var CDirtyProduct $dirtyProduct */
                $dirtyProduct = $dpEm->findOne($dpId);
                $sheetPrototype->productDetailLabel->rewind();

                \Monkey::app()->repoFactory->beginTransaction();
                /** Inserisco la variante*/
                $variant = $variantFactory->getEmptyEntity();
                $variant->name = $dirtyProduct->var;
                $variant->description = $dirtyProduct->extend->colorDescription;


                /** Inserisco il prodotto */
                $product = $productFactory->getEmptyEntity();
                $product->productStatusId = 11;
                $product->itemno = $dirtyProduct->itemno;

                if (!isset($brandDic[$slugify->slugify($dirtyProduct->brand)])) throw new BambooOutOfBoundException('Product Brand not found in Dictionary: %s', [$dirtyProduct->brand]);
                $product->productBrandId = $brandDic[$slugify->slugify($dirtyProduct->brand)];

                $existingProduct = \Monkey::app()->dbAdapter->select('Product',
                    ['itemno' => $product->itemno,
                        'productBrandId' => $product->productBrandId])->fetchAll();
                if (count($existingProduct)) {
                    $product->id = $existingProduct[0]['id'];
                } else if (!is_null($dirtyProduct->relationship)) {
                    if (!is_null($dirtyProduct->relationship->product)) {
                        $product->id = $dirtyProduct->relationship->product->id;
                    } else {
                        throw new BambooLogicException('Cannot find valid product relationship');
                    }
                } else {
                    $product->id = \Monkey::app()->dbAdapter->query("SELECT id FROM Product ORDER BY id DESC LIMIT 0,1", [])->fetch()['id'] + 1;
                }


                /** fixme excludo i prodotti già fusi ma... non so se è sufficiente */
                $conto = \Monkey::app()->dbAdapter->query(" SELECT count(*) AS conto
													FROM Product, ProductVariant
													WHERE Product.productVariantId = ProductVariant.id AND
														  Product.itemno LIKE ? AND
														  Product.productBrandId = ? AND
														  ProductVariant.name LIKE ? AND
														  Product.productStatusId NOT IN (8,13)", [$dirtyProduct->itemno, $product->productBrandId, $variant->name])->fetch()['conto'];
                if ($conto > 0) {
                    /** CHANGE EXECUTION; FUSE AND CONTINUE; END TRANSACTION */
                    if (!isset($seasonDic[$slugify->slugify($dirtyProduct->extend->season)])) throw new BambooOutOfBoundException('Product Season not found in Dictionary: %s', [$dirtyProduct->extend->season]);
                    $newSeason = $seasonDic[$slugify->slugify($dirtyProduct->extend->season)];
                    $this->fuseProduct($product, $variant, $dirtyProduct, $newSeason, $sizeConnector);
                    \Monkey::app()->repoFactory->commit();
                    continue;
                } else {
                    $variant->id = $variant->insert();
                    $product->productVariantId = $variant->id;
                }

                if (!isset($seasonDic[$slugify->slugify($dirtyProduct->extend->season)])) throw new BambooOutOfBoundException('Product Season not found in Dictionary: %s', [$dirtyProduct->extend->season]);
                $product->productSeasonId = $seasonDic[$slugify->slugify($dirtyProduct->extend->season)];
                $product->sortingPriorityId = 99;
                $product->dummyPicture = "bs-dummy-16-9.png";
                /** aggiungo il colore */
                if (!isset($colorGroupDic[$slugify->slugify($dirtyProduct->extend->generalColor)])) throw new BambooOutOfBoundException('Product Color not found in Dictionary: %s', [$dirtyProduct->extend->generalColor]);
                $product->productColorGroupId = $colorGroupDic[$slugify->slugify($dirtyProduct->extend->generalColor)];
                $product->insert();
                $product = $productFactory->findOne($product->getIds());

                /** aggiungo i tag */
                $tags = [];
                $tags[] = $dirtyProduct->extend->tag1;
                $tags[] = $dirtyProduct->extend->tag2;
                $tags[] = $dirtyProduct->extend->tag3;
                foreach ($tags as $tag) {
                    if (empty($tag)) continue;
                    if (!isset($tagDic[$slugify->slugify($tag)])) throw new BambooOutOfBoundException('Product Tag not found in Dictionary: %s', [$tag]);
                    \Monkey::app()->dbAdapter->insert('ProductHasTag',
                        ['productId' => $product->id,
                            'productVariantId' => $product->productVariantId,
                            'tagId' => $tagDic[$slugify->slugify($tag)]], false, true);
                }
                $mandatoryTags = [1, 6];
                foreach ($mandatoryTags as $oneTag) {
                    try {
                        \Monkey::app()->dbAdapter->insert('ProductHasTag', ['productId' => $product->id,
                            'productVariantId' => $product->productVariantId,
                            'tagId' => $oneTag]);
                    } catch (\Throwable $e) {
                    }
                }

                $term = [];
                /** aggiungo la categoria */
                $term[] = $dirtyProduct->extend->audience;
                $term[] = $dirtyProduct->extend->cat1;
                $term[] = $dirtyProduct->extend->cat2;
                $term[] = $dirtyProduct->extend->cat3;
                $term[] = $dirtyProduct->extend->cat4;
                $term[] = $dirtyProduct->extend->cat5;
                $term = implode('-', $term);
                if (!isset($categoryDic[$slugify->slugify($term)])) throw new BambooOutOfBoundException('Product Category not found in Dictionary: %s', [$term]);
                \Monkey::app()->dbAdapter->insert('ProductHasProductCategory', ['productId' => $product->id,
                    'productVariantId' => $product->productVariantId,
                    'productCategoryId' => $categoryDic[$slugify->slugify($term)]]);

                $product->productSizeGroupId = $sizeConnector->findConnectionForProduct($product, $dirtyProduct); //FIXME Will it work?

                if (!is_numeric($product->productSizeGroupId)) throw new BambooOutOfBoundException('Product Size group not found');

                $product->productSheetPrototypeId = 33;
                $product->update();

                $shopHasProduct = $shopProduct->getEmptyEntity();
                $shopHasProduct->productId = $product->id;
                $shopHasProduct->productVariantId = $product->productVariantId;
                $shopHasProduct->shopId = $this->shop->shopId;
                $shopHasProduct->extId = $dirtyProduct->extId;
                $shopHasProduct->productSizeGroupId = $product->productSizeGroupId;
                $shopHasProduct->price = $dirtyProduct->getDirtyPrice();
                $shopHasProduct->salePrice = $dirtyProduct->getDirtySalePrice();
                $shopHasProduct->value = $dirtyProduct->getDirtyValue();
                $shopHasProduct->insert();

                $name = $nameFactory->insertName(trim($dirtyProduct->extend->name));
                try {
                    $nameFactory->saveNameForNewProduct($product->id, $product->productVariantId, $name);
                } catch (\Throwable $e) {
                }


                $productDescriptionTranslation = $descriptionFactory->getEmptyEntity();
                $productDescriptionTranslation->productId = $product->id;
                $productDescriptionTranslation->productVariantId = $product->productVariantId;
                $productDescriptionTranslation->langId = 1;
                $productDescriptionTranslation->marketplaceId = 1;
                $productDescriptionTranslation->description = is_null($dirtyProduct->extend->description) ? "" : $dirtyProduct->extend->description;
                $productDescriptionTranslation->insert();

                foreach ($dirtyProduct->dirtyDetail as $detail) {
                    if (!$sheetPrototype->productDetailLabel->valid()) break;

                    $detailSlug = $slugify->slugify($detail->content);

                    if (empty($detailSlug)) {
                        $sheetPrototype->productDetailLabel->next();
                        continue;
                    }
                    /** insert new detail into table */
                    if (!isset($detailSet[$detailSlug])) {
                        $detailSet[$detailSlug] = \Monkey::app()->dbAdapter->insert('ProductDetail', ['slug' => $detailSlug]);
                        \Monkey::app()->dbAdapter->insert('ProductDetailTranslation', ["productDetailId" => $detailSet[$detailSlug], "langId" => 1, "name" => strip_tags($detail->content) . " !"]);
                    }
                    \Monkey::app()->dbAdapter->insert('ProductSheetActual', [
                        "productId" => $product->id,
                        "productVariantId" => $product->productVariantId,
                        "productDetailLabelId" => $sheetPrototype->productDetailLabel->current()->id,
                        "productDetailId" => $detailSet[$detailSlug]
                    ]);

                    $sheetPrototype->productDetailLabel->next();
                }

                $dirtyProduct->productId = $product->id;
                $dirtyProduct->productVariantId = $product->productVariantId;
                $dirtyProduct->dirtyStatus = 'K';
                $dirtyProduct->update();

                \Monkey::app()->repoFactory->commit();
                $done++;
                $this->report($this::POST, 'Products', 'report', 'Create Products', 'Created new Product: ' . $product->id . '-' . $product->productVariantId, $this->uniqueId, $this->id);

            } catch (BambooOutOfBoundException $e) {
                \Monkey::app()->repoFactory->rollback();
                $this->report($this::POST, 'Products', 'error', 'Create Products', 'Errore in crezione, gestito per ' . $dpId['id'] . ' | ' . $e->getMessage(), $this->uniqueId, $this->id);
                $dictionaryProblem = true;
            } catch (BambooException $e) {
                \Monkey::app()->repoFactory->rollback();
                $this->report($this::POST, 'Products', 'error', 'Create Products', 'Errore in crezione, gestito per ' . $dpId['id'] . ' | ' . $e->getMessage(), $this->uniqueId, $this->id);
            } catch (\ErrorException $e) {
                \Monkey::app()->repoFactory->rollback();
                $this->report($this::POST, 'Products', 'error', 'Errore ErrorException in crezione generico . ' . $dpId['id'] . ' | ' . $e->getMessage(), $this->uniqueId, $this->id);
            } catch (\Throwable $e) {
                \Monkey::app()->repoFactory->rollback();
                $this->report($this::POST, 'Products', 'error', 'Create Products', 'Errore Exception in crezione generico ' . $dpId['id'] . ' | ' . $e->getMessage(), $this->uniqueId, $this->id);

            }
        }
        if ($dictionaryProblem) {
            iwesMail('it@iwes.it',
                'Importer: Errore nella creazione prodotti di ' . $this->shop->name,
                'Verificare i dizionari per la creazione dei prodotti');
        }

        return $done;
    }

    /**
     * @param array $rowDictionary
     * @return array
     * @throws BambooOutOfBoundException
     */
    protected function mapDictionary($name, array $rowDictionary)
    {
        $s = new CSlugify();
        $dic = [];
        $ok = false;
        if (empty($rowDictionary)) {
            throw new BambooOutOfBoundException('Empty Dictionary ' . $name);
        }
        foreach ($rowDictionary as $val) {
            if (!$ok && isset($val['foreignKey']) && !is_null($val['foreignKey']) && !empty($val['foreignKey'])) $ok = true;
            $dic[$s->slugify($val['term'])] = $val['foreignKey'];
        }
        if (!$ok) throw new BambooOutOfBoundException('Empty Simple Dictionary' . $name);

        return $dic;
    }

    /**
     * @param $product
     * @param $variant
     * @param $dirtyProduct
     * @param $newSeasonId
     * @param $sizeConnector
     * @throws BambooLogicException
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    protected function fuseProduct($product, $variant, $dirtyProduct, $newSeasonId, $sizeConnector)
    {
        $existing = \Monkey::app()->repoFactory->create('Product')->findOneBySql("SELECT Product.id, Product.productVariantId
													FROM Product, ProductVariant
													WHERE Product.productVariantId = ProductVariant.id AND
														  Product.itemno LIKE ? AND
														  Product.productBrandId = ? AND
														  ProductVariant.name LIKE ? AND
														  Product.productStatusId NOT IN (8,13)", [$dirtyProduct->itemno, $product->productBrandId, $variant->name]);

        if ($existing) {
            $shp = \Monkey::app()->repoFactory->create('ShopHasProduct')->getEmptyEntity();
            $shp->shopId = $this->shop->shopId;
            $shp->productId = $existing->id;
            $shp->productVariantId = $existing->productVariantId;

            $shp2 = \Monkey::app()->repoFactory->create('ShopHasProduct')->findOne($shp->getIds());
            if (is_null($shp2)) {
                $shp->price = $dirtyProduct->getDirtyPrice();
                $shp->salePrice = $dirtyProduct->getDirtySalePrice();
                $shp->value = $dirtyProduct->getDirtyValue();
                $shp->productSizeGroupId = $sizeConnector->findConnectionForProduct($product, $dirtyProduct);
                if (!is_numeric($shp->productSizeGroupId)) $shp->productSizeGroupId = $product->productSizeGroupId;
                $shp->insert();
            } else {
                $shp2->price = $dirtyProduct->getDirtyPrice();
                $shp2->salePrice = $dirtyProduct->getDirtySalePrice();
                $shp2->value = $dirtyProduct->getDirtyValue();
                $shp2->update();
            }

            $dirtyProduct->productId = $existing->id;
            $dirtyProduct->productVariantId = $existing->productVariantId;
            $dirtyProduct->dirtyStatus = 'K';
            $dirtyProduct->update();

            $this->report($this::POST, 'Products', 'warning', 'Fuse Products', 'Fusing DirtyProduct: ' . $dirtyProduct->id . ' with Product: ' . $existing->printId(), $this->uniqueId, $this->id);

            $product = \Monkey::app()->repoFactory->create('Product')->findOneBy([
                'id' => $dirtyProduct->productId,
                'productVariantId' => $dirtyProduct->productVariantId
            ]);

            if ($product->productSeasonId != $newSeasonId) {
                $this->report($this::POST, 'Products', 'warning', 'Fuse Products', 'Season Change for product:' . $product->printId() . ' from ' . $product->productSeasonId . ' to ' . $newSeasonId, $this->uniqueId, $this->id);

                $productSeason = \Monkey::app()->repoFactory->create('ProductSeason')->findOneBy(['id' => $newSeasonId]);
                if ($productSeason->order > $product->productSeason->order) {
                    $this->report($this::POST, 'Products', 'warning', 'Fuse Products', 'Season Change, the new season is newer, CHANGE!', $this->uniqueId, $this->id);
                    $product->productSeasonId = $newSeasonId;
                    $product->isOnSale = false;
                    $product->update();
                } else {
                    $this->report($this::POST, 'Products', 'warning', 'Fuse Products', 'Season Change, the new season NOT newer no need for update', $this->uniqueId, $this->id);
                }
            }
        } else {
            $this->report($this::POST, 'Products', 'error', 'Fuse Products', 'Error Fusing DirtyProduct: ' . $dirtyProduct->id . ' existing in context...' . ' | ' . $existing, $this->uniqueId, $this->id);
            throw new BambooLogicException("Product already extisting");
        }

    }

    /**
     * @throws BambooOutOfBoundException
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function checkForNewDetails()
    {
        $detailSet = $this->mapDictionary("Detail", \Monkey::app()->dbAdapter->query("SELECT slug AS term, id AS foreignKey FROM ProductDetail WHERE slug != ''", [])->fetchAll());

        $sql = "SELECT DISTINCT(dp.id) AS id
                FROM Product p
                  JOIN ShopHasProduct shp ON (p.id, p.productVariantId) = (shp.productId, shp.productVariantId)
                  JOIN DirtyProduct dp ON shp.productId = dp.productId AND shp.productVariantId = dp.productVariantId AND shp.shopId = dp.shopId
                  JOIN DirtyDetail dd ON dp.id = dd.dirtyProductId
                  LEFT JOIN ProductSheetActual psa ON (p.id,p.productVariantId) = (psa.productId, psa.productVariantId)
                  WHERE shp.shopId = ? AND trim(dd.content) != '' AND CHAR_LENGTH(dd.content) > 2
                GROUP BY p.id, p.productVariantId
                HAVING count(psa.productDetailLabelId) = 0 AND count(dd.id) > 0";

        $dirtyProducts = \Monkey::app()->repoFactory->create('DirtyProduct')->findBySql($sql, [$this->shop->shopId]);
        $this->report($this::POST, 'Products', 'report', 'checkForNewDetails', 'Found ' . $dirtyProducts->count() . ' to work', $this->uniqueId, $this->id);

        $slugify = new CSlugify();
        /** @var CDirtyProduct $dirtyProduct */
        foreach ($dirtyProducts as $dirtyProduct) {
            try {

                $dirtyProduct->product->productSheetPrototypeId = 33;
                $dirtyProduct->product->update();
                $this->report($this::POST, 'Products', 'report', 'checkForNewDetails', 'DirtyProduct ' . $dirtyProduct->id . ' has ' . $dirtyProduct->dirtyDetail->count() . ' details', $this->uniqueId, $this->id);
                $dirtyProduct->product->productSheetPrototype->productDetailLabel->rewind();
                foreach ($dirtyProduct->dirtyDetail as $detail) {
                    if (!$dirtyProduct->product->productSheetPrototype->productDetailLabel->valid()) break;

                    $detailSlug = $slugify->slugify($detail->content);

                    if (empty($detailSlug)) {
                        $dirtyProduct->product->productSheetPrototype->productDetailLabel->next();
                        continue;
                    }
                    /** insert new detail into table */
                    if (!isset($detailSet[$detailSlug])) {
                        $detailSet[$detailSlug] = \Monkey::app()->dbAdapter->insert('ProductDetail', ['slug' => $detailSlug]);
                        \Monkey::app()->dbAdapter->insert('ProductDetailTranslation', ["productDetailId" => $detailSet[$detailSlug], "langId" => 1, "name" => strip_tags($detail->content) . " !"]);
                    }
                    \Monkey::app()->dbAdapter->insert('ProductSheetActual', [
                        "productId" => $dirtyProduct->product->id,
                        "productVariantId" => $dirtyProduct->product->productVariantId,
                        "productDetailLabelId" => $dirtyProduct->product->productSheetPrototype->productDetailLabel->current()->id,
                        "productDetailId" => $detailSet[$detailSlug]
                    ]);

                    $dirtyProduct->product->productSheetPrototype->productDetailLabel->next();
                }
            } catch (\Throwable $e) {
                $this->report($this::POST, 'Products', 'error', 'checkForNewDetails', 'Error writing new detail for ' . $dirtyProduct->id . ' | ' . $e->getMessage(), $this->uniqueId, $this->id);
            }
        }

    }


    /**
     * @return bool
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    private function sendPhotos()
    {
        $this->report($this::POST, 'Products', 'report', 'sendPhotos', 'Starting', $this->uniqueId, $this->id);

        $ftpDestination = new CFTPClient(\Monkey::app(), [
            'host' => 'fiber.office.iwes.it',
            'user' => 'shooting',
            'pass' => 'PBYI34nbf',
            'port' => '21',
            'timeout' => '10',
            'mode' => '2'
        ]);
        $ftpDestDir = '/shootImport/incoming/' . $this->shop->name;
        $ftpDestination->changeDir($ftpDestDir);

        $res = \Monkey::app()->dbAdapter->query(
            "SELECT dpp.id AS id, dpp.dirtyProductId AS dirtyProductID, url, location, position, worked, dpp.shopId AS shopId, p.id AS productId, p.productVariantId FROM DirtyPhoto dpp, DirtyProduct dp, Product p WHERE dpp.dirtyProductId = dp.id AND dp.productId = p.id AND dp.productVariantId = p.productVariantId AND dpp.shopId = ? AND ( dpp.worked = 0 OR dpp.worked IS NULL ) ORDER BY dpp.creationDate DESC",
            [$this->shop->shopId]
        )->fetchAll();
        $this->report($this::POST, 'Products', 'report', 'download immagini', 'inizio', $this->uniqueId, $this->id);

        //creo la cartella
        $destDir = \Monkey::app()->rootPath() . "/temp/tempApiImgs/";
        if (!is_dir(rtrim($destDir, "/"))) mkdir($destDir, 0777, true);

        $i = 0;
        foreach ($res as $k => $v) {
            try {
                if ($i % 50 == 0) $this->report($this::POST, 'Products', 'report', 'download immagini', 'tentate ' . $k . ' immagini', $this->uniqueId, $this->id);

                if (2000 < $i) break;
                /** @var CProduct $p */
                $p = \Monkey::app()->repoFactory->create("Product")->findOneBy(['id' => $v['productId'], 'productVariantId' => $v['productVariantId']]);

                $path = pathinfo($v['url']);

                $imgBody = file_get_contents(htmlspecialchars_decode($v['url']));

                $imgN = str_pad($v['position'], 3, "0", STR_PAD_LEFT);
                $destFileName = $p->getAztecCode() . " - " . $imgN . "." . $path['extension'];
                if ($p->productPhoto->count()) $existing = true;
                else $existing = false;

                $putRes = NULL;
                try {
                    $putRes = file_put_contents($destDir . $destFileName, $imgBody);
                    if ($imgN == 1 && !$existing) {
                        $this->saveDummyPicture($p, $destDir . $destFileName);
                    }
                    if ($putRes) {
                        if ($existing) $ftpDestination->changeDir($ftpDestDir . '/existing');
                        else $ftpDestination->changeDir($ftpDestDir);
                        chmod($destDir . $destFileName, 0777);
                        if ($ftpDestination->put($destDir . $destFileName, $destFileName)) {
                            //segno come "worked" le immagini importate
                            \Monkey::app()->dbAdapter->update("DirtyPhoto", ['worked' => 1], ['id' => $v['id']]);
                        } else {
                            $this->report($this::POST, 'Products', 'error', 'ftp-upload', "file non uploadato sul NAS: " . $ftpDestDir . $destFileName, $this->uniqueId, $this->id);
                        }
                        unlink($destDir . $destFileName);
                    }
                } catch (\Throwable $e) {
                    $this->report($this::POST, 'Products', 'error', 'download immagini', $destFileName . "non salvato. File scaricato, ma impossibile salvarlo su disco. Url corrispondente: " . $v['url'] . ' | ' . $e->getMessage(), $this->uniqueId, $this->id);
                    if (!is_dir(rtrim($destDir, "/"))) mkdir($destDir, 0777, true);
                }

            } catch (\Throwable $e) {
                $this->report($this::POST, 'Products', 'error', 'Downloading Photo', 'generic error: | ' . $e->getMessage(), $this->uniqueId, $this->id);
            }
            $i++;
        }
        try {
            $files = glob($destDir . '*');
            foreach ($files as $file) {
                if (is_file($file))
                    unlink($file);
            }
            rmdir($destDir);
        } catch (\Throwable $e) {
            $this->report($this::POST, 'Products', 'error', 'SendPhotos', 'error while deleting photos | ' . $e->getMessage(), $this->uniqueId, $this->id);
        }
        $this->report($this::POST, 'Products', 'report', 'download immagini', 'fine', $this->uniqueId, $this->id);

        return true;
    }

    /**
     * @param CProduct $p
     * @param $photoPath
     * @throws BambooException
     * @throws \Throwable
     * @throws \bamboo\core\exceptions\RedPandaException
     */
    private function saveDummyPicture(CProduct $p, $photoPath)
    {
        if (empty($p->dummyPicture) || $p->dummyPicture == 'bs-dummy-16-9.png') {
            $dummyFolder = \Monkey::app()->rootPath() . \Monkey::app()->cfg()->fetch('paths', 'dummyFolder') . '/';
            \Monkey::app()->vendorLibraries->load("amazon2723");
            $width = 500;
            $imager = new ImageEditor();
            $fileName = pathinfo($photoPath);
            $dummyName = rand(0, 9999999999) . '.' . $fileName['extension'];
            try {

                if (!$imager->load($photoPath)) throw new BambooException('Could not load image. Photopath: ' . $photoPath);
                $imager->resizeToWidth($width);
                $imager->save($dummyFolder . '/' . $dummyName);
                $p->dummyPicture = $dummyName;
                $p->update();
                $this->report($this::POST, 'Products', 'report', 'PhotoDownload', 'Set dummyPicture: ' . $dummyName . ' for: ' . $p->printId(), $this->uniqueId, $this->id);
            } catch (\Throwable $e) {
                $this->report($this::POST, 'Products', 'warning', 'PhotoDownload', 'Failed setting dummyPicture: ' . $dummyName . ' for ' . $p->printId(), $this->uniqueId, $this->id);
                throw $e;
            }

        }
    }

    private function saveFile()
    {
        //$doneFolder = \Monkey::app()->rootPath() . \Monkey::app()->cfg()->fetch('paths', 'productSync') . '/' . $this->shop->name . '/import/done/';
        $doneFolder = \Monkey::app()->rootPath() . "/temp/tempApiImgs/";
        $file = $doneFolder . time() . '.json';
        file_put_contents($file, json_encode($this->data['json']));

        $now = new \DateTime();
        $zipName = $doneFolder . $now->format('YmdHis') . '_' . pathinfo($file)['filename'] . '.tar';
        $phar = new \PharData($zipName);

        $phar->addFile($file, pathinfo($file)['basename']);

        if ($phar->count() > 0) {
            /** @var \PharData $compressed */
            $compressed = $phar->compress(\Phar::GZ);
            if (file_exists($compressed->getPath())) {
                unlink($file);
                return $zipName;
            }
        }

        return true;
    }
}