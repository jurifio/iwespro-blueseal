<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\db\pandaorm\entities\CEntityManager;
use bamboo\core\db\pandaorm\entities\IEntity;
use bamboo\core\exceptions\RedPandaException;
use bamboo\core\traits\TFormInputValidate;
use bamboo\core\utils\slugify\CSlugify;

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
        /** LOGICHE DI UPDATE*/
        try {
            $productIds = array("id" => $post['Product_id'], "productVariantId" => $post['Product_productVariantId']);
            $productIdsExt = array("productId" => $post['Product_id'], "productVariantId" => $post['Product_productVariantId']);
            /** @var CEntityManager $em */

            $productEdit = $this->app->repoFactory->create('Product')->findOne($productIds);
            /** INIZIO TRANSACTION */
            if (!$this->app->dbAdapter->beginTransaction()) throw new \Exception();

            /** UPDATE VARIANTE */
	        $productVariant = $productEdit->productVariant;
	        $productVariant->name = $post['ProductVariant_name'];
	        $productVariant->description = $post['ProductVariant_description'];
	        $productVariant->update();
            //$variantId = $this->app->dbAdapter->update("ProductVariant", ["name" => $post['ProductVariant_name'], "description" => $post['ProductVariant_description']], array("id" => $post['Product_productVariantId']));

            /** UPDATE PRODUCT */
            if (isset($files['Product_dummyPicture']) && isset($files['Product_dummyPicture']['name']) && !empty($files['Product_dummyPicture']['name'])) {
                /** PRENDO E RINOMINO LA FOTO */
                $name = pathinfo($files['Product_dummyPicture']['name']);
                $uploadfile = rand(0, 9999999999) . '.' . $name['extension'];
                if (!rename($files['Product_dummyPicture']['tmp_name'], $fileFolder . $uploadfile)) throw new \Exception();
	            $productEdit->dummyPicture = $uploadfile;
            }
	        $productEdit->lastUpdate = date("Y-m-d H:i:s");
	        $productEdit->itemno = $post['Product_itemno'];

            $hasShop = 0;


            /** AGGIORNO I PREZZI */
            $shp = $this->app->repoFactory->create('ShopHasProduct');
            if (array_key_exists('Shop_id', $post)) {
                if ($post['Shop_id']) {
                    $shpe = $shp->findOneBy(
                        [
                            'productId' => $productEdit->id,
                            'productVariantId' => $productEdit->productVariantId,
                            'shopId' => $post['Shop_id']
                        ]
                    );
                    if ($shpe) {
                        $shpe->price = $post['Product_retail_price'];
                        $shpe->value = $post['Product_value'];
                        $shpe->update();
                    }
                }
            } else {
                //se non c'è il campo shop o non è selezionato uno shop, vengono assegnati tutti gli shop dell'utente
                $user = $this->app->getUser();
                foreach($user->shop as $shop) {
                    $shpe = $shp->findOneBy(
                        [
                            'productId' => $productIdsExt['productId'],
                            'productVariantId' => $productIdsExt['productVariantId'],
                            'shopId' => $shop->id,
                        ]);
                    $shpe->price = $post['Product_retail_price'];
                    $shpe->value = $post['Product_value'];
                    $shpe->update();
                }
            }

            //$productId = $this->app->dbAdapter->update("Product", $updateData, $productIds);
            $this->app->dbAdapter->commit();

            /** INIZIO TRANSACTION PER IL CARICAMENTO DEI VALORI FACOLTATIVI DI PRODOTTO E DI DETTAGLI PRODOTTO */
            if (!$this->app->dbAdapter->beginTransaction()) throw new \Exception();
            /** UPDATE PRODUCT */
            if ($this->isValidInput("Product_productBrandId", $post)) {
	            $productEdit->productBrandId = $post['Product_productBrandId'];
                //$this->app->dbAdapter->update("Product", array("productBrandId" => $post['Product_productBrandId']), $productIds);
            }
            if ($this->isValidInput("Product_productSeasonId", $post)) {
	            $productEdit->productSeasonId = $post['Product_productSeasonId'];
                //$this->app->dbAdapter->update("Product", array("productSeasonId" => $post['Product_productSeasonId']), $productIds);
            }
            if ($this->isValidInput("Product_sortingPriorityId", $post)) {
	            $productEdit->sortingPriorityId = $post['Product_sortingPriorityId'];
                //$this->app->dbAdapter->update("Product", array("sortingPriorityId" => $post['Product_sortingPriorityId']), $productIds);
            }
            /*if ($this->isValidInput("Product_externalId", $post)) {
	            $productEdit->externalId = $post['Product_externalId'];
                //$this->app->dbAdapter->update("Product", array("externalId" => $post['Product_externalId']), $productIds);
            }*/
            if ($this->isValidInput("Product_sizes", $post)) {
	            $productEdit->productSizeGroupId = $post['Product_sizes'];
                //$this->app->dbAdapter->update("Product", array("sizeGroupId" => $post['Product_sizes']), $productIds);
            }
	        if ($this->isValidInput("Product_note", $post)) {
		        $productEdit->note = $post['Product_note'];
		        //$this->app->dbAdapter->update("Product", array("note" => $post['Product_note']), $productIds);
	        }

	        $productEdit->update();

            if ($this->isValidInput("Tag_names", $post)) {
                $this->app->dbAdapter->delete("ProductHasTag", $productIdsExt,'AND', true);
                foreach ($post['Tag_names'] as $tag) {
                    $input = $productIdsExt;
                    $input['tagId'] = $tag;
                    $this->app->dbAdapter->insert("ProductHasTag", $input);
                }
            }
            if ($this->isValidInput("ProductColorGroup_id", $post)) {
                $this->app->dbAdapter->delete("ProductHasProductColorGroup", $productIdsExt,'AND', true);
                $data = $productIdsExt;
                $data['productColorGroupId'] = $post['ProductColorGroup_id'];
                $this->app->dbAdapter->insert("ProductHasProductColorGroup", $data);
            }

            /** UPDATE DEI DETTAGLI PRODOTTO */
            if ($this->isValidInput("Product_dataSheet", $post)) {
	            $detailRepo = $this->app->repoFactory->create('ProductDetail');
	            $detailTranslationRepo = $this->app->repoFactory->create('ProductDetailTranslation');
	            $productSheetActualRepo = $this->app->repoFactory->create('ProductSheetActual');
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
            foreach ($post as $key => $input) {
                $inputName = explode('_', $key);
                if ($inputName[0] != 'ProductName') continue;

                $productNameTranslation = $productEdit->productNameTranslation->findOneByKey('langId',$inputName[1]);
	            if($productNameTranslation){
		            $productNameTranslation->name = $input;
					$productNameTranslation->update();
	            } else {
		            try {
			            $this->app->dbAdapter->insert("ProductNameTranslation", $productIdsExt+['langId'=>$inputName[1],'name'=>$input]);
		            } catch (\Exception $e) {
		            }
	            }
            }
            /** UPDATE DESCRIZIONE PRODOTTO */
            foreach ($post as $key => $input) {
                $inputName = explode('_', $key);
                if ($inputName[0] != 'ProductDescription') continue;
	            $productDescriptionTranslation = $productEdit->productDescriptionTranslation->findOneByKeys(['langId'=>$inputName[1],'marketplaceId'=>1]);
	            if($productDescriptionTranslation instanceof IEntity){
		            $productDescriptionTranslation->description = $input;
		            $productDescriptionTranslation->update();
	            } else {
		            $productDescriptionTranslation = $this->app->repoFactory->create('ProductDescriptionTranslation')->getEmptyEntity();
		            $productDescriptionTranslation->langId = $inputName[1];
		            $productDescriptionTranslation->marketplaceId = 1;
		            $productDescriptionTranslation->description = $input;
		            $productDescriptionTranslation->productId = $productEdit->id;
		            $productDescriptionTranslation->productVariantId = $productEdit->productVariantId;
		            $productDescriptionTranslation->insert();
                }
            }
            /** INSERIMENTO SHOP */
            foreach ($post as $key => $input) {
                $inputName = explode('_', $key);
                if ($inputName[0] != 'Shop') continue;
                $insertData = array();
                if ($input == 0) break;
                $insertData['shopId'] = $input;
                try {
                    $this->app->dbAdapter->insert("ShopHasProduct", ($insertData + $productIdsExt));
                } catch (\Exception $e) {
                    //fixme gestisci multinegozio
                }
            }

            $this->app->dbAdapter->commit();

            if ($this->isValidInput("Product_status", $post)) {
                if($post['Product_status'] == 6 &&
	                (is_null($productEdit->productPhoto) || $productEdit->productPhoto->isEmpty() ||
                   is_null($productEdit->productSku) || $productEdit->productSku->isEmpty())) {
	                throw new RedPandaException('Impossibile pubblicare un prodotto incompleto');
                }
	            $productEdit->productStatusId = $post['Product_status'];
	            $productEdit->update();
            }
            $ret = ['code' => $productIds, 'message' => 'Il prodotto è stato aggiornato correttamente.'];
            return json_encode($ret);
        } catch (\Exception $e) {
            $this->app->dbAdapter->rollBack();
            throw $e;
        }
    }

    public function post()
    {
        $fileFolder = $this->app->rootPath().$this->app->cfg()->fetch('paths', 'dummyFolder') . '/';
        $post = $this->app->router->request()->getRequestData();
        $files = $this->app->router->request()->getFiles();
        $productId = 0;

        $prodRepo = $this->app->repoFactory->create('Product');

        if (isset($post['button']) && $post['button'] == 'hide' && isset($post['dirtyProductId'])) {
            $this->app->dbAdapter->query("UPDATE DirtyProduct SET dirtyStatus = 'N' WHERE id = ?", [$post['dirtyProductId']]);
            throw new RedPandaException('errore nel salvataggio');
        }

        /** INSERIMENTO DATI DI BASE */
        try {

            /** INIZIO TRANSACTION */
            if (!$this->app->dbAdapter->beginTransaction()) throw new \Exception();

            /** CONTROLLO SE IL PRODOTTO ESISTE GIA' */

            // CONTROLLO SE DEVO CREARE UNA VARIANTE O UN NUOVO PRODOTTO
            $productControl = $this->app->dbAdapter->query("SELECT * FROM Product WHERE itemno = ? AND productBrandId = ?", [$post['Product_itemno'], $post['Product_productBrandId']])->fetch();
            if ($productControl) {
                $productId =  $productControl['id'];
            }
            unset($productControl);


            /** INSERISCO IL PRODOTTO DI BASE */
	        $var = $this->app->repoFactory->create('ProductVariant')->getEmptyEntity();
	        $var->name = $post['ProductVariant_name'];
	        $var->description = $post['ProductVariant_description'];
	        $variantId = $var->insert();


                /** LOGICA DI INSERIMENTO */
            if (isset($files['Product_dummyPicture']) && isset($files['Product_dummyPicture']['name']) && !empty($files['Product_dummyPicture']['name'])) {
                /** PRENDO E RINOMINO LA FOTO */
                $name = pathinfo($files['Product_dummyPicture']['name']);
                $uploadfile = rand(0, 9999999999) . '.' . $name['extension'];
                if (!rename($files['Product_dummyPicture']['tmp_name'], $fileFolder . $uploadfile)) {
                   throw new \Exception();
                }
            } else {
                $uploadfile = 'bs-dummy-16-9.png';
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

            $hasShop = 0;
            foreach ($post as $key => $input) {
                $inputName = explode('_', $key);
                if ($inputName[0] != 'Shop') continue;
                if (!$input) continue;

                $insertData = $productIdsExt;
                $insertData['shopId'] = $input;
                $insertData['price'] = $post['Product_retail_price'];
                $insertData['value'] = $post['Procuct_value'];
                $this->app->dbAdapter->insert("ShopHasProduct", $insertData);
                $hasShop++;
            }

            //se non c'è il campo shop o non è selezionato uno shop, vengono assegnati tutti gli shop dell'utente
            if (!$hasShop) {
                $user = $this->app->getUser();
                $shp = $this->app->repoFactory->create('ShopHasProduct');
                foreach($user->shop as $shop) {
                    $shpe = $shp->getEmptyEntity();
                    $shpe->productId = $productIdsExt['productId'];
                    $shpe->productVariantId = $productIdsExt['productVariantId'];
                    $shpe->shopId = $shop->id;
                    if (array_key_exists('Product_retail_price', $post)) $shpe->price = $post['Product_retail_price'];
                    if (array_key_exists('Product_value', $post)) $shpe->value = $post['Product_value'];
                    $shpe->insert();
                }
            }

            $this->app->dbAdapter->commit();

            /** INIZIO TRANSACTION PER IL CARICAMENTO DEI VALORI FACOLTATIVI DI PRODOTTO E DI DETTAGLI PRODOTTO */
            if (!$this->app->dbAdapter->beginTransaction()) throw new \Exception();

            /** UPDATE PRODUCT */
            $productNew = $this->app->repoFactory->create("Product")->findOneBy(['id' => $productId, 'productVariantId' => $variantId]);

            if ($this->isValidInput('Product_productSeasonId', $post)) {
                $productNew->productSeasonId = $post['Product_productSeasonId'];
               // $this->app->dbAdapter->update("Product", array("productSeasonId" => $post['Product_productSeasonId']), $productIds);
            }

            if ($this->isValidInput('Product_status', $post) && $post['Product_status'] != 6) {
                $productNew->productStatusId = $post['Product_status'];
               // $this->app->dbAdapter->update("Product", array("status" => $post['Product_status']), $productIds);
            }

            if ($this->isValidInput('Product_externalId', $post)) {
                $productNew->externalId = $post['Product_externalId'];
                //$this->app->dbAdapter->update("Product", array("externalId" => $post['Product_externalId']), $productIds);
            }
            if ($this->isValidInput('Product_sortingPriorityId', $post)) {
                $productNew->sortingPriorityId = $post['Product_sortingPriorityId'];
                //$this->app->dbAdapter->update("Product", array("sortingPriorityId" => $post['Product_sortingPriorityId']), $productIds);
            }
            if ($this->isValidInput('Product_sizes', $post)) {
                $productNew->productSizeGroupId = $post['Product_sizes'];
                //$this->app->dbAdapter->update("Product", array("sizeGroupId" => $post['Product_sizes']), $productIds);
            }
            if ($this->isValidInput('Product_note', $post)) {
                $productNew->note = $post['Product_note'];
                //$this->app->dbAdapter->update("Product", array("note" => $post['Product_note']), $productIds);
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
                $insertColor = array();
                $insertColor = $productIdsExt;
                $insertColor['productColorGroupId'] = $post['ProductColorGroup_id'];
                $this->app->dbAdapter->insert("ProductHasProductColorGroup", $insertColor);
            }

	        $slugify = new CSlugify();
            /** INSERIMENTO DETTAGLI PRODOTTO */
	        if ($this->isValidInput("Product_dataSheet", $post)) {
		        $productSheetActualRepo = $this->app->repoFactory->create('ProductSheetActual');
		        /** INSERIMENTO DETTAGLI PRODOTTO */

		        $productNew->productSheetPrototypeId = $post['Product_dataSheet'];
		        $productNew->update();

		        foreach ($post as $key => $input) {
			        $inputName = explode('_', $key);
			        if (($inputName[0] != 'ProductDetail') || ($input == '0')) continue;
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
            foreach ($post as $key => $input) {
                $inputName = explode('_', $key);
                if ($inputName[0] != 'ProductName') continue;
                $insertData = array();
                $insertData = $productIdsExt;
                $insertData['langId'] = $inputName[1];
                $insertData['name'] = $input;
                $this->app->dbAdapter->insert("ProductNameTranslation", $insertData);
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

            $this->app->dbAdapter->commit();
        } catch (\Exception $e) {
            $this->app->dbAdapter->rollBack();
            throw $e;
        }
        $ret = ['code' => $productIds, 'message' => 'Il prodotto è stato inserito. Ora puoi lavorare sulle quantità'];
        return json_encode($ret);
    }
}