<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\db\pandaorm\entities\CEntityManager;
use bamboo\core\db\pandaorm\entities\IEntity;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\RedPandaAssetException;
use bamboo\core\exceptions\RedPandaException;
use bamboo\core\traits\TFormInputValidate;
use bamboo\core\utils\amazonPhotoManager\ImageManager;
use bamboo\core\utils\slugify\CSlugify;
use bamboo\domain\entities\CShopHasProduct;
use bamboo\core\utils\amazonPhotoManager\S3Manager;
use bamboo\core\base\CObjectCollection;
use PDO;
use PDOException;
use Throwable;

/**
 * Class CProductAddController
 * @package bamboo\blueseal\controllers
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CProductManageController extends ARestrictedAccessRootController
{
    use TFormInputValidate;

    protected $fallBack = "blueseal";

    public function put()
    {
        $fileFolder = $this->app->rootPath().$this->app->cfg()->fetch('paths', 'dummyFolder') . '/';
        $post = $this->app->router->request()->getRequestData();
        $files = $this->app->router->request()->getFiles();
        $shops = $this->app->getUser()->shop;
        foreach($shops as $v) {
            $shop = $v;
            break;
        }

        /** LOGICHE DI UPDATE*/
        try {
            $context = 'Start';
            $productIds = array("id" => $post['Product_id'], "productVariantId" => $post['Product_productVariantId']);
            $productIdsExt = array("productId" => $post['Product_id'], "productVariantId" => $post['Product_productVariantId']);
            /** @var CEntityManager $em */

            $productEdit = \Monkey::app()->repoFactory->create('Product')->findOne($productIds);
            $findOldProductBrand=\Monkey::app()->repoFactory->create('ProductBrand')->findOneBy(['id'=>$productEdit->productBrandId]);
            $oldFolder=$findOldProductBrand->slug;
            $findNewProductBrand=\Monkey::app()->repoFactory->create('ProductBrand')->findOneBy(['id'=>$post['Product_productBrandId']]);
            $newFolder=$findNewProductBrand->slug;
            /** INIZIO TRANSACTION */
            if (!\Monkey::app()->repoFactory->beginTransaction()) throw new \Exception();

            /** UPDATE VARIANTE */
            $context = 'ProductVariant';
            $productVariant = $productEdit->productVariant;
            $productVariant->name = $post['ProductVariant_name'];
            $productVariant->description = $post['ProductVariant_description'];
            $productVariant->update();
            $variantId = $this->app->dbAdapter->update("ProductVariant", ["name" => $post['ProductVariant_name'], "description" => $post['ProductVariant_description']], array("id" => $post['Product_productVariantId']));

            /** UPDATE PRODUCT */

            $context = 'Product First Update';
            if ($this->isValidInput("Product_dummyPicture", $post)) {
                $productEdit->dummyPicture = $post['Product_dummyPicture'];
            }

            $productId = $this->app->dbAdapter->update("Product", $updateData, $productIds);
            \Monkey::app()->repoFactory->commit();

            /** INIZIO TRANSACTION PER IL CARICAMENTO DEI VALORI FACOLTATIVI DI PRODOTTO E DI DETTAGLI PRODOTTO */
            $context = 'Product second update';
            if (!\Monkey::app()->repoFactory->beginTransaction()) throw new \Exception();
            /** UPDATE PRODUCT */
            if ($this->isValidInput("Product_productBrandId", $post)) {
                $productEdit->productBrandId = $post['Product_productBrandId'];
                //$this->app->dbAdapter->update("Product", array("productBrandId" => $post['Product_productBrandId']), $productIds);
            }
            if ($this->isValidInput("Product_productSeasonId", $post)) {
                $productEdit->productSeasonId = $post['Product_productSeasonId'];
                $this->app->dbAdapter->update("Product", array("productSeasonId" => $post['Product_productSeasonId']), $productIds);
            }
            if ($this->isValidInput("Product_sortingPriorityId", $post)) {
                $productEdit->sortingPriorityId = $post['Product_sortingPriorityId'];
                $this->app->dbAdapter->update("Product", array("sortingPriorityId" => $post['Product_sortingPriorityId']), $productIds);
            }
            if ($this->isValidInput("Product_externalId", $post)) {
	            $productEdit->externalId = $post['Product_externalId'];
                $this->app->dbAdapter->update("Product", array("externalId" => $post['Product_externalId']), $productIds);
            }
            if ($this->isValidInput("Product_sizes", $post)) {
                $productEdit->productSizeGroupId = $post['Product_sizes'];
                $this->app->dbAdapter->update("Product", array("productSizeGroupId" => $post['Product_sizes']), $productIds);
            }
            if ($this->isValidInput("Product_note", $post)) {
                $productEdit->note = $post['Product_note'];
                $this->app->dbAdapter->update("Product", array("note" => $post['Product_note']), $productIds);
            }


            $productEdit->update();

            $context = "Tag Insert";
            if ($this->isValidInput("Tag_names", $post)) {
                $this->app->dbAdapter->delete("ProductHasTag", $productIdsExt,'AND', true);
                foreach ($post['Tag_names'] as $tag) {
                    $input = $productIdsExt;
                    $input['tagId'] = $tag;
                    $this->app->dbAdapter->insert("ProductHasTag", $input);
                }
            }
            if ($this->isValidInput("ProductColorGroup_id", $post)) {
                $productEdit->productColorGroupId = $post['ProductColorGroup_id'];
                $productEdit->update();
            }

            /** UPDATE DEI DETTAGLI PRODOTTO */
            $context = "detail Update";
            if ($this->isValidInput("Product_dataSheet", $post)) {
                $detailRepo = \Monkey::app()->repoFactory->create('ProductDetail');
                $detailTranslationRepo = \Monkey::app()->repoFactory->create('ProductDetailTranslation');
                $productSheetActualRepo = \Monkey::app()->repoFactory->create('ProductSheetActual');
                /** INSERIMENTO DETTAGLI PRODOTTO */
                if($post['Product_dataSheet'] != $productEdit->productSheetPrototypeId) {
                    foreach($productEdit->productSheetActual as $val) $val->delete();
                    $productEdit->productSheetPrototypeId = $post['Product_dataSheet'];
                    unset($productEdit->productSheetPrototype);
                    $productEdit->update();
                }

                foreach ($post as $key => $input) {
                    $inputName = explode('_', $key);
                    if ($inputName[0] != 'ProductDetail') continue;

                    /** cerco all'interno della sheet se esiste già un dettaglio con lo stesso label e lo cancella */
                    $actual = $productEdit->productSheetActual->findOneByKey('productDetailLabelId', $inputName[2]);
                    if ($actual) $actual->delete();
                    if (0 == $input) continue;

                    $actual = $productSheetActualRepo->getEmptyEntity();
                    $actual->productId = $productEdit->id;
                    $actual->productVariantId = $productEdit->productVariantId;
                    $actual->productDetailLabelId = $inputName[2];
                    $actual->productDetailId = $input;
                    $actual->insert();
                }
            }

            $context = "Category Update";
            if ($this->isValidInput('ProductCategory_id', $post)) {
                $cats = $post['ProductCategory_id'];
            } else {
                $cats = 1;
            }

            $this->app->dbAdapter->delete("ProductHasProductCategory", $productIdsExt,'AND',true);
            $datas = explode(",", $cats);
            foreach ($datas as $cat) {
                $updateData = $productIdsExt;
                $updateData['productCategoryId'] = $cat;
                $this->app->dbAdapter->insert("ProductHasProductCategory", $updateData);
            }

            /** UPDATE NOME PRODOTTO */
            $context = "Product Name Update";
            $pntRepo = \Monkey::app()->repoFactory->create('ProductNameTranslation');
            foreach ($post as $key => $input) {
                $inputName = explode('_', $key);
                if ($inputName[0] != 'ProductName') continue;
                $pntRepo->updateProductName($productEdit->id, $productEdit->productVariantId, $input);
            }
            /** UPDATE DESCRIZIONE PRODOTTO */
            $context = "Description Update";
            foreach ($post as $key => $input) {
                $inputName = explode('_', $key);
                if ($inputName[0] != 'ProductDescription') {
                    continue;
                } else {
                    $productDescriptionTranslation = \Monkey::app()->repoFactory->create('ProductDescriptionTranslation')->findOneBy(['productId'=>$productEdit->id,'productVariantId'=>$productEdit->productVariantId,'langId' => $inputName[1],'marketplaceId' => 1]);
                    if($productDescriptionTranslation){
                        $productDescriptionTranslation->description = $input;
                        $productDescriptionTranslation->update();
                    } else {
                        $productDescriptionTranslation = \Monkey::app()->repoFactory->create('ProductDescriptionTranslation')->getEmptyEntity();
                        $productDescriptionTranslation->langId = $inputName[1];
                        $productDescriptionTranslation->marketplaceId = 1;
                        $productDescriptionTranslation->description = $input;
                        $productDescriptionTranslation->productId = $productEdit->id;
                        $productDescriptionTranslation->productVariantId = $productEdit->productVariantId;
                        $productDescriptionTranslation->insert();
                    }
                }
            }
            /** INSERIMENTO SHOP */
            $context = "Shop Update";
            foreach ($post as $key => $input) {
                $inputName = explode('_', $key);
                if ($inputName[0] != 'Shop') continue;
                $insertData = array();
                if ($input == 0) break;
                $insertData['shopId'] = $input;
                try {
                    $this->app->dbAdapter->insert("ShopHasProduct", ($insertData + $productIdsExt));
                } catch (\Throwable $e) {
                    //fixme gestisci multinegozio
                }
            }


            $user = $this->app->getUser();
            if (!$user->hasPermission('allShops')) {
                foreach ($user->shop as $s) {
                    $shopId = $s->id;
                }
                $shpRepo = \Monkey::app()->repoFactory->create('ShopHasProduct');
                $shp = $shpRepo->findOneBy(['productId' => $productEdit->id, 'productVariantId' => $productEdit->productVariantId, 'shopId' => $shopId]);
                if ($shp) {
                    $shp->extId = $post['Product_extId'];
                    $shp->update();
                } else {
                    $shp = $shp->getEmptyEntity();
                    $shp = $shp->productId = $productEdit->id;
                    $shp = $shp->productVariantId = $productEdit->productVariantId;
                    $shp = $shp->shopId = $shopId;
                    $shp->extId = $post['Product_extId'];
                    $shp->insert();
                }
                /** @var CShopHasProduct $shp */
                $shp->updatePrices($post['Product_value'], $post['Product_retail_price']);
            }


            if ((array_key_exists('Product_retail_price',$post)) && (array_key_exists('Product_value',$post))) {
                $skus = \Monkey::app()->repoFactory->create('ProductSku')->findBy([
                    'productId' => $productIds['id'],
                    'productVariantId' => $productIds['productVariantId'],
                    'shopId' => $shop->id
                ]);
                foreach($skus as $s){
                    $s->value = $post['Product_value'];
                    $s->price = $post['Product_retail_price'];
                    $s->update();
                }
            }

            \Monkey::app()->repoFactory->commit();

            $context = "Product Update Final";
            if ($this->isValidInput("Product_status", $post)) {
                $productEdit->productStatusId = $post['Product_status'];
                $productEdit->update();
            }

            $productEdit->lastUpdate = date("Y-m-d H:i:s");
            $productEdit->itemno = $post['Product_itemno'];
            $productEdit->update();
            $ret = ['code' => $productIds, 'message' => 'Il prodotto è stato aggiornato correttamente.'];
            $productPhotoRepo=\Monkey::app()->repoFactory->create('ProductPhoto');
            $productId=$productEdit->id;
            $productVariantId=$productEdit->productVariantId;
            $productHasProductPhotos=\Monkey::app()->repoFactory->create('ProductHasProductPhoto')->findBy(['productId'=>$productId,'productVariantId'=>$productVariantId]);
            foreach($productHasProductPhotos as $productHasProductPhoto){
                $photo=$productPhotoRepo->findOneBy(['id'=>$productHasProductPhoto->productPhotoId]);
                $urlOrigin="https://cdn.iwes.it/".$oldFolder.'/'.$photo->name;

                //salvataggio immagine da cartella ordini
                $local = $this->app->rootPath() . "/temp-movePhoto/";
                $imageDownload = file_get_contents($urlOrigin);
                file_put_contents($local.$photo->name, $imageDownload);



                // upload immagine su cartella destinazione

                \Monkey::app()->vendorLibraries->load("amazon2723");
                $config = $this->app->cfg()->fetch('miscellaneous', 'amazonConfiguration');
                $tempFolder = $this->app->rootPath().$this->app->cfg()->fetch('paths', 'tempFolder').'-movePhoto'."/";

                $image = new ImageManager(new S3Manager($config['credential']), $this->app, $tempFolder);

                try{
                    $res = $image->S3UploadBrand($config['bucket'],$photo->name, $newFolder);
                }catch(RedPandaAssetException $e){
                    $this->app->router->response()->raiseProcessingError();
                    return 'Non riesco a Copiare il file';
                }

                unlink($local.$photo->name);



            }
            $shopRepo = \Monkey ::app() -> repoFactory -> create('Shop') -> findBy(['hasEcommerce' => 1]);

            foreach ($shopRepo as $value) {
                /********marketplace********/
                $db_host = $value->dbHost;
                $db_name = $value->dbName;
                $db_user = $value->dbUsername;
                $db_pass = $value->dbPassword;
                $shop = $value->id;
                $shopName = $value->name;
                try {

                    $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
                    $db_con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                    $res = " connessione ok <br>";
                } catch (PDOException $e) {
                    $res = $e->getMessage();
                }
                try {
                    $stmtFindProduct = $db_con->prepare('SELECT count(*) as isexist from Product WHERE id=' . $productId . ' and productVariantId=' . $productVariantId);
                    $stmtFindProduct->execute();
                    $rowFindProduct = $stmtFindProduct->fetch(PDO::FETCH_ASSOC);
                    if ($rowFindProduct['isexist'] > 0) {
                        $stmtUpdateProduct = $db_con->prepare('UPDATE Product set 
                        itemno=' . $post['Product_itemno'] . ', 
                        productSeasonId=' . $post['Product_productSeasonId'] . ',
                        productBrandId=' . $post['Product_productBrandId'] . ',
                        productColorGroupId=' . $post['ProductColorGroup_id'] . ',
                        sortingPriorityId=' . $post['Product_sortingPriorityId'] . ',
                        productSizeGroupId=' . $post['Product_sizes'] . ',
                        productStatusId=' . $post['Product_status'] . '  WHERE id=' . $productId . ' and productVariantId=' . $productVariantId);
                        $stmtUpdateProduct->execute();
                        $findProductNameTranslation = \Monkey::app()->repoFactory->create('ProductNameTranslation')->findOneBy(['productId' => $productId,'productVariantId' => $productVariantId]);
                        foreach ($findProductNameTranslation as $findProductNameTranslations) {
                            $langId = $findProductNameTranslations->langId;
                            try {
                                $stmtUpdateProductNameTranslation = $db_con->prepare('UPDATE ProductNameTranslation SET `name`=\'' . $findProductNameTranslation->name . '\' WHERE
                             productId=' . $productId . ' and productVariantId=' . $productVariantId . ' and langId=' . $langId);
                                $stmtUpdateProductNameTranslation->execute();
                            } catch (\Throwable $e) {
                                \Monkey::app()->applicationLog('CProductManageController ','error','not Update ProductNameTranslation','cannot update' . $productId . '-' . $productVariantId . ' on ' . $shopName,'');
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    \Monkey::app()->applicationLog('CProductManageController ','error','not Update','cannot update' . $productId . '-' . $productVariantId . ' on ' . $shopName,'');
                }
            }
            return json_encode($ret);
        } catch (\Throwable $e) {
            \Monkey::app()->repoFactory->rollback();

            $ret = [];
            if ($productIds) $ret['code'] = $productIds;
            $ret['message'] = '[' . $context .'] ' . $e->getMessage();
            return json_encode($ret);
        }
    }

    public function post()
    {
        $fileFolder = $this->app->rootPath().$this->app->cfg()->fetch('paths', 'dummyFolder') . '/';
        $post = $this->app->router->request()->getRequestData();
        $files = $this->app->router->request()->getFiles();
        $productId = 0;

        $prodRepo = \Monkey::app()->repoFactory->create('Product');

        if (isset($post['button']) && $post['button'] == 'hide' && isset($post['dirtyProductId'])) {
            $this->app->dbAdapter->query("UPDATE DirtyProduct SET dirtyStatus = 'N' WHERE id = ?", [$post['dirtyProductId']]);
            throw new RedPandaException('errore nel salvataggio');
        }

        /** INSERIMENTO DATI DI BASE */
        try {

            /** INIZIO TRANSACTION */
            if (!\Monkey::app()->repoFactory->beginTransaction()) throw new \Exception();

            /** CONTROLLO SE IL PRODOTTO ESISTE GIA' */

            // CONTROLLO SE DEVO CREARE UNA VARIANTE O UN NUOVO PRODOTTO
            $productControl = $this->app->dbAdapter->query("SELECT * FROM Product WHERE itemno = ? AND productBrandId = ?",[$post['Product_itemno'],$post['Product_productBrandId']])->fetch();
            if ($productControl) {
                $productId = $productControl['id'];
            }
            unset($productControl);


            /** INSERISCO IL PRODOTTO DI BASE */
            $var = \Monkey::app()->repoFactory->create('ProductVariant')->getEmptyEntity();
            $var->name = $post['ProductVariant_name'];
            $var->description = $post['ProductVariant_description'];
            $variantId = $var->insert();


            /** LOGICA DI INSERIMENTO */
            if ($this->isValidInput("Product_dummyPicture",$post)) {
                if ($post['Product_dummyPicture']!=''){
                    $uploadfile = $post['Product_dummyPicture'];
            } else {
                $uploadfile = 'bs-dummy-16-9.png';
            }
        }

            if (!$productId) {
                $productId = $this->app->dbAdapter->query('SELECT max(id) as maxId FROM Product', [])->fetch()['maxId'] + 1;
            }

            $insertData = array();
            $insertData['id'] = $productId;
            $insertData['creationDate'] = date("Y-m-d H:i:s");
            $insertData['productVariantId'] = $variantId;
            $insertData['itemno'] = $post['Product_itemno'];
            $insertData['dummyPicture'] = $uploadfile;
            $insertData['productBrandId'] = $post['Product_productBrandId'];
            $this->app->dbAdapter->insert("Product", $insertData);
            $productIds = array("id" => $productId, "productVariantId" => $variantId);
            $productIdsExt = array("productId" => $productId, "productVariantId" => $variantId);

            /** INSERIMENTO SHOP */

            $user = $this->app->getUser();
            if (!$user->hasPermission('allShops')) {
                foreach ($user->shop as $s) {
                    $shopId = $s->id;
                    break;
                }
                $shpRepo = \Monkey::app()->repoFactory->create('ShopHasProduct');
                $shp = $shpRepo->getEmptyEntity();
                $shp->productId = $productId;
                $shp->productVariantId = $variantId;
                $shp->shopId = $shopId;
                $shp->extId = $post['Product_extId'];
                $shp->insert();

                $shp = $shpRepo->findOneBy(['productId' => $productId, 'productVariantId' => $variantId, 'shopId' => $shopId]);
                $shp->updatePrices($post['Product_value'], $post['Product_retail_price']);
            }

            \Monkey::app()->repoFactory->commit();

            /** INIZIO TRANSACTION PER IL CARICAMENTO DEI VALORI FACOLTATIVI DI PRODOTTO E DI DETTAGLI PRODOTTO */
            if (!\Monkey::app()->repoFactory->beginTransaction()) throw new \Exception();

            /** UPDATE PRODUCT */
            $productNew = \Monkey::app()->repoFactory->create("Product")->findOneBy(['id' => $productId, 'productVariantId' => $variantId]);

            if ($this->isValidInput('Product_productSeasonId', $post)) {
                $productNew->productSeasonId = $post['Product_productSeasonId'];
                 $this->app->dbAdapter->update("Product", array("productSeasonId" => $post['Product_productSeasonId']), $productIds);
            }

            if ($this->isValidInput('Product_status', $post) && $post['Product_status'] != 6) {
                $productNew->productStatusId = $post['Product_status'];
                $this->app->dbAdapter->update("Product", array("productStatusId" => $post['Product_status']), $productIds);
            }

            if ($this->isValidInput('Product_externalId', $post)) {
                $productNew->externalId = $post['Product_externalId'];
                $this->app->dbAdapter->update("Product", array("externalId" => $post['Product_externalId']), $productIds);
            }
            if ($this->isValidInput('Product_sortingPriorityId', $post)) {
                $productNew->sortingPriorityId = $post['Product_sortingPriorityId'];
                $this->app->dbAdapter->update("Product", array("sortingPriorityId" => $post['Product_sortingPriorityId']), $productIds);
            }
            if ($this->isValidInput('Product_sizes', $post)) {
                $productNew->productSizeGroupId = $post['Product_sizes'];
                $this->app->dbAdapter->update("Product", array("productSizeGroupId" => $post['Product_sizes']), $productIds);
            }
            if ($this->isValidInput('Product_note', $post)) {
                $productNew->note = $post['Product_note'];
                $this->app->dbAdapter->update("Product", array("note" => $post['Product_note']), $productIds);
            }

            if ($this->isValidInput('Tag_names', $post)) {
                foreach ($post['Tag_names'] as $tag) {
                    if ($tag == 1) continue;
                    $this->app->dbAdapter->insert("ProductHasTag", array("productId" => $productId, "productVariantId" => $variantId, "tagId" => $tag));
                }
            } else {
                $this->app->dbAdapter->insert("ProductHasTag", array("productId" => $productId, "productVariantId" => $variantId, "tagId" => 1));
            }

            if ($this->isValidInput('ProductColorGroup_id', $post)) {
                $productNew->productColorGroupId = $post['ProductColorGroup_id'];
            }

            $slugify = new CSlugify();
            /** INSERIMENTO DETTAGLI PRODOTTO */
            if ($this->isValidInput("Product_dataSheet", $post)) {
                $productSheetActualRepo = \Monkey::app()->repoFactory->create('ProductSheetActual');
                /** INSERIMENTO DETTAGLI PRODOTTO */

                $productNew->productSheetPrototypeId = $post['Product_dataSheet'];
                $productNew->update();

                foreach ($post as $key => $input) {
                    $inputName = explode('_', $key);
                    if (($inputName[0] != 'ProductDetail') || ($input == '0') || ($input == '')) continue;
                    /** cerco il valore del dettaglio $detail */
                    $sheet = $productSheetActualRepo->getEmptyEntity();
                    $sheet->productId = $productNew->id;
                    $sheet->productVariantId = $productNew->productVariantId;
                    $sheet->productDetailLabelId = $inputName[2];
                    $sheet->productDetailId = $input;
                    $sheet->insert();
                }
            }

            $productNew->update();

            /** INIZIO INSERIMENTO CATEGORIA PRODOTTO */
            if ($this->isValidInput('ProductCategory_id', $post)) {
                $cats = $post['ProductCategory_id'];
            } else {
                $cats = 1;
            }
            $datas = explode(",", $cats);
            foreach ($datas as $cat) {
                $updateData = array();
                $updateData = $productIdsExt;
                $updateData['productCategoryId'] = $cat;
                $this->app->dbAdapter->insert("ProductHasProductCategory", $updateData);
            }

            /** INSERIMENTO NOME PRODOTTO */
            $pntRepo = \Monkey::app()->repoFactory->create('ProductNameTranslation');
            foreach ($post as $key => $input) {
                $inputName = explode('_', $key);
                if ($inputName[0] != 'ProductName') continue;
                $pntRepo->saveProductName($productNew->id, $productNew->productVariantId, $input);
            }

            /** INSERIMENTO DESCRIZIONE PRODOTTO */
            foreach ($post as $key => $input) {
                $inputName = explode('_', $key);
                if ($inputName[0] != 'ProductDescription') continue;
                $insertData = array();
                $insertData = $productIdsExt;
                $insertData['langId'] = $inputName[1];
                $insertData['marketplaceId'] = 1;
                $insertData['description'] = $input;
                $this->app->dbAdapter->insert("ProductDescriptionTranslation", $insertData);
            }

            \Monkey::app()->repoFactory->commit();
        } catch (\Throwable $e) {
            \Monkey::app()->repoFactory->rollback();

            $ret = [];
            if ($productIds) $ret['code'] = $productIds;
            $ret['message'] = $e->getMessage();
            return json_encode($ret);
        }
        $ret = ['code' => $productIds, 'message' => 'Il prodotto è stato inserito. Ora puoi lavorare sulle quantità'];
        return json_encode($ret);
    }
}