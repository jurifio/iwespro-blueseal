<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\db\pandaorm\entities\CEntityManager;
use bamboo\core\exceptions\RedPandaException;
use bamboo\core\traits\TFormInputValidate;

/**
 * Class CDescriptionTranslateManageController
 * @package redpanda\blueseal\controllers
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CDescriptionTranslateManageController extends ARestrictedAccessRootController
{
    use TFormInputValidate;

    protected $fallBack = "blueseal";

    public function put()
    {

        $post = $this->app->router->request()->getRequestData();
        $productId = $this->app->router->request()->getRequestData('Product_id');
        $productVariantId = $this->app->router->request()->getRequestData('Product_variantId');

        /** LOGICHE DI UPDATE*/
        try {
            $description=[];
            foreach ($post as $key=>$val){
                $k = explode('_',$key);
                if ($k[0] != 'ProductDescription') continue;
                $description[$k[1]] = $val;
            }
\BlueSeal::dump($description);
            throw new \Exception();
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
	        $this->app->repoFactory->create('ProductVariant')->update($productVariant);
            //$variantId = $this->app->dbAdapter->update("ProductVariant", ["name" => $post['ProductVariant_name'], "description" => $post['ProductVariant_description']], array("id" => $post['Product_productVariantId']));

            /** UPDATE PRODUCT */
            $updateData = array();
            if (isset($files['Product_dummyPicture']) && isset($files['Product_dummyPicture']['name']) && !empty($files['Product_dummyPicture']['name'])) {
                /** PRENDO E RINOMINO LA FOTO */
                $name = pathinfo($files['Product_dummyPicture']['name']);
                $uploadfile = rand(0, 9999999999) . '.' . $name['extension'];
                if (!rename($files['Product_dummyPicture']['tmp_name'], $fileFolder . $uploadfile)) throw new \Exception();
	            $productEdit->dummyPicture = $uploadfile;
            }
	        $productEdit->lastUpdate = date("Y-m-d H:i:s");
	        $productEdit->itemno = $post['Product_itemno'];

            //$productId = $this->app->dbAdapter->update("Product", $updateData, $productIds);
            $ress = $this->app->dbAdapter->commit();

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
            if ($this->isValidInput("Product_externalId", $post)) {
	            $productEdit->externalId = $post['Product_externalId'];
                //$this->app->dbAdapter->update("Product", array("externalId" => $post['Product_externalId']), $productIds);
            }
            if ($this->isValidInput("Product_sizes", $post)) {
	            $productEdit->sizeGroupId = $post['Product_sizes'];
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
                /** INSERIMENTO DETTAGLI PRODOTTO */
                $this->app->dbAdapter->delete("ProductHasProductAttributeValue", $productIdsExt,'AND', true);
	            $productEdit->sheetName = $post['Product_dataSheet'];
	            $productEdit->update();
                //$this->app->dbAdapter->update("Product", array("sheetName" => $post['Product_dataSheet']), $productIds);
                foreach ($post as $key => $input) {
                    $inputName = explode('_', $key);
                    if ($inputName[0] != 'ProductDetail') continue;
                    $attrbuteValue = $this->app->repoFactory->create('ProductAttributeValue')->findOneBy(['langId' => $inputName[1], 'productAttributeId' => $inputName[2], 'name' => trim($input)]);
                    if (is_null($attrbuteValue)) {
                        $attrbuteValueId = $this->app->dbAdapter->insert('ProductAttributeValue', ['langId' => $inputName[1], 'productAttributeId' => $inputName[2], 'name' => trim($input)]);
                    } else {
	                    $attrbuteValueId  = $attrbuteValue->id;
                    }
                    $insertData = $productIdsExt;
                    $insertData['productAttributeId'] = $inputName[2];
                    $insertData['productAttributeValueId'] = $attrbuteValueId;
                    $this->app->dbAdapter->insert("ProductHasProductAttributeValue", $insertData);
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

                $productName = $productEdit->productName->findOneByKey('langId',$inputName[1]);
	            if($productName){
		            $productName->name = $input;
					$productName->update();
	            } else {
		            try {
			            $this->app->dbAdapter->insert("ProductName", $productIdsExt+['langId'=>$inputName[1],'name'=>$input]);
		            } catch (\Exception $e) {
		            }
	            }
            }
            /** UPDATE DESCRIZIONE PRODOTTO */
            foreach ($post as $key => $input) {
                $inputName = explode('_', $key);
                if ($inputName[0] != 'ProductDescription') continue;
                $updateData = array();
	            $productDescription = $productEdit->productDescription->findOneByKeys(['langId'=>$inputName[1],'marketplaceId'=>1]);
	            if($productDescription){
		            $productDescription->description = $input;
		            $productDescription->update();
	            } else {
		            $where = array();
		            $where = $productIdsExt;
		            $where['langId'] = $inputName[1];
		            $where['marketplaceId'] = 1;
		            $where['description'] = $input;
		            try {
			            $this->app->dbAdapter->insert("ProductDescription", $updateData + $where);
		            } catch (\Exception $e) {}
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
                if($post['Product_status'] == 'P' &&
	                (is_null($productEdit->productPhoto) || $productEdit->productPhoto->isEmpty() ||
                   is_null($productEdit->productSku) || $productEdit->productSku->isEmpty())) {
	                throw new RedPandaException('Impossibile pubblicare un prodotto incompleto');
                }
	            $productEdit->status = $post['Product_status'];
	            $productEdit->update();
            }

            echo json_encode($productIds);
        } catch (\Exception $e) {
            $this->app->dbAdapter->rollBack();
            throw $e;
        }
    }

    public function post()
    {
        $fileFolder = $this->app->cfg()->fetch('paths', 'dummyFolder') . '/';
        $post = $this->app->router->request()->getRequestData();
        $files = $this->app->router->request()->getFiles();

        if (isset($post['button']) && $post['button'] == 'hide' && isset($post['dirtyProductId'])) {
            $this->app->dbAdapter->query("UPDATE DirtyProduct SET dirtyStatus = 'N' WHERE id = ?", [$post['dirtyProductId']]);
            throw new RedPandaException('errore nel salvataggio');
        }

        /** INSERIMENTO DATI DI BASE */
        try {

            /** INIZIO TRANSACTION */
            if (!$this->app->dbAdapter->beginTransaction()) throw new \Exception();

            /** CONTROLLO SE IL PRODOTTO ESISTE GIA' */
            $conto = $this->app->dbAdapter->query("SELECT count(*) AS conto FROM Product, ProductVariant WHERE Product.productVariantId = ProductVariant.id AND Product.itemno LIKE ? AND Product.productBrandId = ? AND ProductVariant.name LIKE ? ", array($post['Product_itemno'], $post['Product_productBrandId'], $post['ProductVariant_name']))->fetch()['conto'];
            if ($conto > 0) {
                echo '<br>prodotto già esistente:';
                echo '<br>brand: ' . $post['Product_productBrandId'];
                echo '<br>cpf: ' . $post['Product_itemno'];
                echo '<br>var: ' . $post['ProductVariant_name'];
                echo '<br>altri valori:<br>';
                throw new RedPandaException('Existing Product');
            }

            /** INSERISCO IL PRODOTTO DI BASE */
	        $var = $this->app->repoFactory->create('ProductVariant')->getEmptyEntity();
	        $var->name = $post['ProductVariant_name'];
	        $var->description = $post['ProductVariant_description'];
	        $var->insert();

            $variantId = $this->app->dbAdapter->insert("ProductVariant", ["name" => $post['ProductVariant_name'], "description" => $post['ProductVariant_description']]);

            if (isset($post['Product_id'])) {
                /** LOGICA DI DUPLICAZIONE */
                $productId = $post['Product_id'];

                if (isset($files['Product_dummyPicture']) && isset($files['Product_dummyPicture']['name']) && !empty($files['Product_dummyPicture']['name'])) {
                    /** PRENDO E RINOMINO LA FOTO */
                    $name = pathinfo($files['Product_dummyPicture']['name']);
                    $uploadfile = rand(0, 9999999999) . '.' . $name['extension'];
                    if (!rename($files['Product_dummyPicture']['tmp_name'], $fileFolder . $uploadfile)) throw new \Exception();
                } else {
                    $uploadfile = $this->app->dbAdapter->query("SELECT dummyPicture FROM Product WHERE id = ? ORDER BY id ASC LIMIT 0,1", array($productId))->fetch()['dummyPicture'];
                }

            } else {
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
                try {
                    $productId = $this->app->dbAdapter->query("SELECT id FROM Product WHERE itemno = ? AND productBrandId = ? ORDER BY id DESC LIMIT 0,1", array($post['Product_itemno'], $post['Product_productBrandId']))->fetch()['id'];
                } catch (\Exception $e) {
                }
                if (!isset($productId) || !is_numeric($productId)) {
                    $productId = $this->app->dbAdapter->query("SELECT id FROM Product ORDER BY id DESC LIMIT 0,1", array())->fetch()['id'] + 1;
                }
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
            foreach ($post as $key => $input) {
                $inputName = explode('_', $key);
                if ($inputName[0] != 'Shop') continue;
                $insertData = array();
                $insertData = $productIdsExt;
                $insertData['shopId'] = $input;
                $this->app->dbAdapter->insert("ShopHasProduct", $insertData);
            }
            if (isset($post['dirtyProductId'])) {
                try {

                    $dirtyProduct = $this->app->repoFactory->create("DirtyProduct")->findOneBy(['id' => $post['dirtyProductId']]);
                    $dirtyProduct->productId = $productId;
                    $dirtyProduct->productVariantId = $variantId;
                    $dirtyProduct->dirtyStatus = 'K';
                    $this->app->repoFactory->create("DirtyProduct")->update($dirtyProduct);

                } catch (\Exception $e) {
                    $this->app->router->response()->raiseUnauthorized();
                }
                //$this->app->dbAdapter->update('DirtyProduct', ['productId' => $productId, 'productVariantId' => $variantId, 'dirtyStatus' => 'K'], ['id' => $post['dirtyProductId']]);
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

            if ($this->isValidInput('Product_status', $post) && $post['Product_status'] != 'P') {
                $productNew->status = $post['Product_status'];
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
                $productNew->sizeGroupId = $post['Product_sizes'];
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

            /** INSERIMENTO DETTAGLI PRODOTTO */
            if ($this->isValidInput('Product_dataSheet', $post)) {
                $productNew->sheetName = $post['Product_dataSheet'];
                //$this->app->dbAdapter->update("Product", array("sheetName" => $post['Product_dataSheet']), $productIds);
                foreach ($post as $key => $input) {
                    $inputName = explode('_', $key);
                    if ($inputName[0] != 'ProductDetail') continue;
                    $attrbuteValue = $this->app->dbAdapter->select('ProductAttributeValue', ['langId' => $inputName[1], 'productAttributeId' => $inputName[2], 'name' => trim($input)])->fetchAll();
                    if (count($attrbuteValue) == 0) {
                        $this->app->dbAdapter->insert('ProductAttributeValue', ['langId' => $inputName[1], 'productAttributeId' => $inputName[2], 'name' => trim($input)]);
                        $attrbuteValue = $this->app->dbAdapter->select('ProductAttributeValue', ['langId' => $inputName[1], 'productAttributeId' => $inputName[2], 'name' => trim($input)])->fetchAll();
                    }
                    $insertData = $productIdsExt;
                    $insertData['productAttributeId'] = $inputName[2];
                    $insertData['productAttributeValueId'] = $attrbuteValue[0]['id'];

                    $this->app->dbAdapter->insert("ProductHasProductAttributeValue", $insertData);
                }
            }

            $this->app->repoFactory->create("Product")->update($productNew);

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
                $this->app->dbAdapter->insert("ProductName", $insertData);
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
                $this->app->dbAdapter->insert("ProductDescription", $insertData);
            }

            $this->app->dbAdapter->commit();
        } catch (\Exception $e) {
            var_dump($e);
            $this->app->dbAdapter->rollBack();
            throw $e;
        }

        echo json_encode($productIds);
    }
}