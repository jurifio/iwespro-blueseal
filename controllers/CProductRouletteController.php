<?php
namespace bamboo\blueseal\controllers;

use bamboo\domain\entities\CShop;
use bamboo\domain\repositories\CProductHistoryRepo;
use bamboo\ecommerce\views\VBase;
use bamboo\core\base\CObjectCollection;
use bamboo\core\exceptions\RedPandaException;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\offline\productsync\import\antonacci\CAntonacciImport;
use bamboo\offline\productsync\import\dellamartira\CDellaMartiraImport;
use bamboo\offline\productsync\import\thesquare\CThesquareImport;

/**
 * Class CProductEditController
 * @package bamboo\blueseal\controllers
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since 1.0
 */
class CProductRouletteController extends CProductManageController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "product_roulette";

    /**
     * @throws \Exception
     * @throws \bamboo\core\exceptions\RedPandaDBALException
     * @throws \bamboo\core\exceptions\RedPandaORMException
     */
    public function get()
	{
		$view = new VBase([]);
		$view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/product_roulette.php');

		/** LOGICA */
		$bluesealBase = $this->app->baseUrl(false) . '/blueseal/';
		$fileFolder = $this->app->rootPath().$this->app->cfg()->fetch('paths', 'dummyFolder') . '/';
		$dummyUrl = $this->app->rootPath().$this->app->cfg()->fetch('paths', 'dummyUrl') . '/';
		$elenco = $bluesealBase . "prodotti";
		$nuovoprodotto = $bluesealBase . "prodotti/roulette";
		$productEdit = null;
		$productRand = null;
		$qrMessage = null;

		$em = $this->app->entityManagerFactory->create('ProductBrand');
		$brands = $em->findAll(null, 'order by `name`');

		$em = $this->app->entityManagerFactory->create('Lang');
		$langs = $em->findAll();

		$em = $this->app->entityManagerFactory->create('ProductSeason');
		$seasons = $em->findAll();

		$em = $this->app->entityManagerFactory->create('ProductSizeGroup');
		$sizesGroups = $em->findAll(null, 'order by locale, macroName, `name`');

		$em = $this->app->entityManagerFactory->create('Shop');
		$shops = $em->findAll(null, 'order by `name`');

		$em = $this->app->entityManagerFactory->create('Tag');
		$tag = $em->findAll(null, 'order by `slug`');

		$em = $this->app->entityManagerFactory->create('ProductColorGroup');
		$gruppicolore = $em->findBySql("SELECT * FROM ProductColorGroup WHERE langId = 1 ORDER BY `name`", []);

        $em = $this->app->entityManagerFactory->create('ProductSheetPrototype');
        $productSheets = $em->findAll();

		$em = $this->app->entityManagerFactory->create('ProductStatus');
		$productStatuses = $em->findAll('limit 99', '');

		$statuses = [];
		$statuses['selected'] = 2;
		foreach ($productStatuses as $status) {
			$statuses[$status->code] = $status->name;
		}

		if (empty($_GET['roulette'])) {
			switch (rand(1, 3)) {
				case 1:
					$shop = 'cartechini';
					break;
				case 2:
					$shop = 'dellamartira';
					break;
				case 3:
					$shop = 'mataloni';
					break;
				default:
					$shop = 'dellamartira';
					break;
			}
		} else {
			$shop = $_GET['roulette'];
		}
		$rand = $this->app->dbAdapter->query("SELECT dp.*
                                              FROM DirtyProduct dp, DirtySku ds, Shop s
                                              WHERE dp.id = ds.dirtyProductId AND
                                              ds.shopId = s.id AND
                                              dp.productId IS NULL AND
                                              dp.productVariantId IS NULL AND
                                              s.name = ? AND
                                              dp.dirtyStatus in ('E', '', 'F') GROUP BY dp.id LIMIT 0, 300", [$shop])->fetchAll();
		if (count($rand) == 0) {
			echo 'Nessun prodotto da Aggiungere';
			die;
		}
		$productRand = $rand[rand(0, (count($rand) - 1))];

		$repo = $this->app->entityManagerFactory->create('Product', false);
		$productEdit = $repo->getEmptyEntity();
		$productEdit->dirtyProductId = $productRand['id'];
		$productEdit->itemno = $productRand['itemno'];
		$productEdit->externalId = $productRand['extId'];
		$temp = new \stdClass();
		$temp->name = $productRand['var'];

		switch ($shop) {
			case 'dellamartira':
				$productEdit->dummyPicture = pathinfo(CDellaMartiraImport::getDummyPic($productEdit->dirtyProductId,$this->app))['basename'];
				break;
			case 'antonacci':
				$pic = CAntonacciImport::getDummyPic($productEdit->dirtyProductId,$this->app);
				if($pic){
					$productEdit->dummyPicture = pathinfo($pic)['basename'];
				}
				break;
			case 'thesquare':
				$pic = CThesquareImport::getDummyPic($productEdit->dirtyProductId,$this->app);
				if($pic){
					$productEdit->dummyPicture = pathinfo($pic)['basename'];
				}
				break;
		}

		$productEdit->productVariant = $temp;
		$productEdit->productBrandId = 1;
		$productEdit->note = $productRand['text'];
		$productEdit->shop = new CObjectCollection();
		$temp = new CShop();
		$temp->id = $productRand['shopId'];
		$productEdit->shop->add($temp);
		$detailsGroups = [];
		//FIXME to test when we have datas
		if (isset($productEdit) && isset($productEdit->sheetName)) {
			$em = $this->app->entityManagerFactory->create('ProductAttribute');
			foreach ($langs as $lang) {
				$sql = 'SELECT productAttributeId AS id FROM ProductSheetPrototype WHERE `name` = "' . $productEdit->sheetName . '"  ';
				$detailsGroups[$lang->lang] = $em->findBySql($sql);
			}
		}
		/** RIEMPIO LE NOTE CON TUTTI I CAMPI UTILI DI DIRTY PRODUCT */
		$datas = [];
		$datas['brand'] = $productRand['brand'];

		$sizes = $this->app->dbAdapter->query("SELECT size FROM DirtySku WHERE dirtyProductId = ?", [$productRand['id']])->fetchAll();
		$sizesPlain = [];
		foreach ($sizes as $a) {
			$sizesPlain[] = $a['size'];
		}

		/**
		 * Fix per testo illeggibile
		 */
		$textlist = "<ul class='roulette-importer-details'>";
		if (stristr($productRand['text'], ';')) {
			$texts = explode(';', $productRand['text']);
		} else if (stristr($productRand['text'], ' - ')) {
			$texts = explode(' - ', $productRand['text']);
		} else {
			$texts = explode(',',$productRand['text']);
		}
		foreach ($texts as $text) {
			if (stristr($text,'.jpg') || mb_strlen($text) === 0) {
				continue;
			}
			$textlist .= "<li>".$text."</li>";
		}
		$textlist .= "</ul>";

	    $datas['taglie'] = implode('-',$sizesPlain);
	    $datas['dettagli'] = $textlist;
	    $productEdit->rouletteNotes = $datas;

        echo $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'statuses' => $statuses,
            'tags' => $tag,
            'dummyUrl' => $dummyUrl,
            'fileFolder' => $fileFolder,
            'elenco' => $elenco,
            'shops' => $shops,
            'nuovoprodotto' => $nuovoprodotto,
            'qrMessage' => $qrMessage,
            'productEdit' => $productEdit,
            'brands' => $brands,
            'langs' => $langs,
            'seasons' => $seasons,
            'bluesealBase' => $bluesealBase,
            'sizesGroups' => $sizesGroups,
            'gruppicolore' => $gruppicolore,
            'productSheets' => $productSheets,
            'detailsGroups' => $detailsGroups,
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }

	/**
	 * @return bool
	 * @throws \Exception
	 */
	public function put()
	{
        $history = new CProductHistoryRepo($this->app->entityManagerFactory->create('ProductHistory'),$this->app);

		$post = $this->app->router->request()->getRequestData();

		$dirtyProduct = $this->app->repoFactory->create('DirtyProduct')->findOne(['id'=>$post['dirtyProductId']]);

		$dirtyProduct->productId = $post['productId'];
		$dirtyProduct->productVariantId = $post['productVariantId'];
		$dirtyProduct->dirtyStatus = 'K';

		$this->app->dbAdapter->beginTransaction();
		$this->app->dbAdapter->insert('ShopHasProduct', ['productId'=>$dirtyProduct->productId,'productVariantId'=>$dirtyProduct->productVariantId,'shopId'=>$dirtyProduct->shopId]);
		$due = $dirtyProduct->update();

        $history->add($post['productId'],$post['productVariantId'],$this->app->getUser()->getId(),'Prodotto unito','DirtyProduct '.$post['dirtyProductId']);

		if ($due == 1) {
			$this->app->dbAdapter->commit();
			return true;
		}
		$this->app->dbAdapter->rollBack();
		throw new \Exception('no updated rows');
	}

	/**
	 * @throws RedPandaException
	 * @throws \Exception
	 */
	public function post()
	{
		$fileFolder = $this->app->cfg()->fetch('paths', 'dummyFolder') . '/';
		$post = $this->app->router->request()->getRequestData();
		$files = $this->app->router->request()->getFiles();
        $history = new CProductHistoryRepo($this->app->entityManagerFactory->create('ProductHistory'),$this->app);

		if (isset($post['button']) && $post['button'] == 'hide' && isset($post['dirtyProductId'])) {
			$this->app->dbAdapter->query("UPDATE DirtyProduct SET dirtyStatus = 'N' WHERE id = ?", [$post['dirtyProductId']]);
			throw new RedPandaException('errore nel salvataggio');
		}

		/** INSERIMENTO DATI DI BASE */
		try {
			/** INIZIO TRANSACTION */
			if (!$this->app->dbAdapter->beginTransaction()) throw new \Exception();

			/** CONTROLLO SE IL PRODOTTO ESISTE GIA' */
			$conto = $this->app->dbAdapter->query("SELECT count(*) AS conto FROM Product, ProductVariant WHERE Product.productVariantId = ProductVariant.id AND Product.itemno LIKE ? AND Product.productBrandId = ? AND ProductVariant.name LIKE ? ", [$post['Product_itemno'],$post['Product_productBrandId'],$post['ProductVariant_name']])->fetch()['conto'];
			if ($conto > 0) {
				$shopInput = [];
				foreach ($post as $key => $input) {
					$inputName = explode('_', $key);
					if ($inputName[0] != 'Shop') continue;
					$shopInput['shopId'] = $input;
				}
				try{
					$existingProduct = $this->app->dbAdapter->query("SELECT Product.id, Product.productVariantId FROM Product, ProductVariant WHERE Product.productVariantId = ProductVariant.id AND Product.itemno LIKE ? AND Product.productBrandId = ? AND ProductVariant.name LIKE ? ",[$post['Product_itemno'],$post['Product_productBrandId'],$post['ProductVariant_name']])->fetchAll()[0];

					$shopInput['dirtyProductId'] = $post['dirtyProductId'];
                    $shopInput['productId'] = $existingProduct['id'];
					$shopInput['productVariantId'] = $existingProduct['productVariantId'];
					$shopInput['response'] = 'duplicate';
					$shopInput['url'] = $this->app->baseUrl(false).'/blueseal/prodotti/modifica?id='.$shopInput['productId'].'&productVariantId='.$shopInput['productVariantId'];

					echo json_encode($shopInput);
					$this->app->router->response()->setBody(json_encode($shopInput));
					$this->app->router->response()->raiseProcessingError()->sendHeaders();
					return true;
				} catch(\Exception $e) {};

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
					$uploadfile = $this->app->dbAdapter->query("SELECT dummyPicture FROM Product WHERE id = ? ORDER BY id ASC LIMIT 0,1", [$productId])->fetch()['dummyPicture'];
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
					$productId = $this->app->dbAdapter->query("SELECT id FROM Product WHERE itemno = ? AND productBrandId = ? ORDER BY id DESC LIMIT 0,1", [$post['Product_itemno'], $post['Product_productBrandId']])->fetch()['id'];
				} catch (\Exception $e) {
				}
				if (!isset($productId) || !is_numeric($productId)) {
					$productId = $this->app->dbAdapter->query("SELECT id FROM Product ORDER BY id DESC LIMIT 0,1", [])->fetch()['id'] + 1;
				}
			}

			$insertData = [];
			$insertData['id'] = $productId;
			$insertData['creationDate'] = date("Y-m-d H:i:s");
			$insertData['productVariantId'] = $variantId;
			$insertData['itemno'] = $post['Product_itemno'];
			$insertData['dummyPicture'] = $uploadfile;
			$insertData['productBrandId'] = $post['Product_productBrandId'];
			$this->app->dbAdapter->insert("Product", $insertData);
            $history->add($productId,$variantId,$this->app->getUser()->getId(),'Prodotto creato');
			$productIds = ["id" => $productId, "productVariantId" => $variantId];
			$productIdsExt = ["productId" => $productId, "productVariantId" => $variantId];

			/** INSERIMENTO SHOP */
			foreach ($post as $key => $input) {
				$inputName = explode('_', $key);
				if ($inputName[0] != 'Shop') continue;
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
			}
			$this->app->dbAdapter->commit();

			/** INIZIO TRANSACTION PER IL CARICAMENTO DEI VALORI FACOLTATIVI DI PRODOTTO E DI DETTAGLI PRODOTTO */
			if (!$this->app->dbAdapter->beginTransaction()) throw new \Exception();

			/** UPDATE PRODUCT */
			$productNew = $this->app->repoFactory->create("Product")->findOneBy(['id' => $productId, 'productVariantId' => $variantId]);

			if ($this->isValidInput('Product_productSeasonId', $post)) {
				$productNew->productSeasonId = $post['Product_productSeasonId'];
			}

			if ($this->isValidInput('Product_status', $post) && $post['Product_status'] != 6) {
				$productNew->productStatusId = $post['Product_status'];
			}

			if ($this->isValidInput('Product_externalId', $post)) {
				$productNew->externalId = $post['Product_externalId'];
			}

			if ($this->isValidInput('Product_sortingPriorityId', $post)) {
				$productNew->sortingPriorityId = $post['Product_sortingPriorityId'];
			}

			if ($this->isValidInput('Product_sizes', $post)) {
				$productNew->productSizeGroupId = $post['Product_sizes'];
			}

			if ($this->isValidInput('Product_note', $post)) {
				$productNew->note = $post['Product_note'];
			}

			if ($this->isValidInput('Tag_names', $post)) {
				foreach ($post['Tag_names'] as $tag) {
					if ($tag == 1) continue;
					$this->app->dbAdapter->insert("ProductHasTag", ["productId" => $productId, "productVariantId" => $variantId, "tagId" => $tag]);
				}
			} else {
				$this->app->dbAdapter->insert("ProductHasTag", ["productId" => $productId, "productVariantId" => $variantId, "tagId" => 1]);
				$this->app->dbAdapter->insert("ProductHasTag", ["productId" => $productId, "productVariantId" => $variantId, "tagId" => 6]);
			}

			if ($this->isValidInput('ProductColorGroup_id', $post)) {
				$insertColor = $productIdsExt;
				$insertColor['productColorGroupId'] = $post['ProductColorGroup_id'];
				$this->app->dbAdapter->insert("ProductHasProductColorGroup", $insertColor);
			}

			/** INSERIMENTO DETTAGLI PRODOTTO */
			if ($this->isValidInput('Product_dataSheet', $post)) {
				$productNew->sheetName = $post['Product_dataSheet'];
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
				$updateData = $productIdsExt;
				$updateData['productCategoryId'] = $cat;
				$this->app->dbAdapter->insert("ProductHasProductCategory", $updateData);
			}

			/** INSERIMENTO NOME PRODOTTO */
			foreach ($post as $key => $input) {
				$inputName = explode('_', $key);
				if ($inputName[0] != 'ProductNameTranslation') continue;
				$insertData = $productIdsExt;
				$insertData['langId'] = $inputName[1];
				$insertData['name'] = $input;
				$this->app->dbAdapter->insert("ProductNameTranslation", $insertData);
			}

			/** INSERIMENTO DESCRIZIONE PRODOTTO */
			foreach ($post as $key => $input) {
				$inputName = explode('_', $key);
				if ($inputName[0] != 'ProductDescription') continue;
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

	public function delete()
	{
		$asd = $this->app->router->request()->getRequestData('dirtyProductId');
        $dirtyProduct = $this->app->repoFactory->create("DirtyProduct")->findOneBy(['id' => $asd]);
        $dirtyProduct->dirtyStatus = 'N';
        echo $this->app->repoFactory->create("DirtyProduct")->update($dirtyProduct);
		return;
	}
}