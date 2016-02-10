<?php
namespace bamboo\blueseal\jobs;

use bamboo\ecommerce\domain\entities\CProduct;
use bamboo\core\db\pandaorm\entities\AEntity;
use bamboo\core\jobs\ACronJob;
use bamboo\core\theming\CWidgetHelper;

/**
 * Class CDispatchPreorderToFriend
 * @package redpanda\blueseal\jobs
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CKleidooFeedBluilder extends ACronJob
{
	protected $marketCat = [];

	public function run($args = null)
	{
		$this->log('Report','Run','Starting To build');

		$minized = $args == 'minified';
		/** PREPARAZIONE */

		$helper = new CWidgetHelper($this->app);
		$categoriesName = $this->app->dbAdapter->query("SELECT productCategoryId, name FROM ProductCategoryHasLang where langId = ?",[$this->app->getLang()->getId()])->fetchAll();
		$categories=[];
		foreach($categoriesName as $alls){
			$categories[$alls['productCategoryId']] = $alls['name'];
		}
		unset($categoriesName);

		/** TROVO PRODOTTI */
		$productRepo = $this->app->repoFactory->create('Product');
		$products = $this->app->dbAdapter->query("SELECT DISTINCT product AS id, variant AS productVariantId FROM vProductSortingView",[])->fetchAll();

		$uri = $this->app->cfg()->fetch('paths','tempFolder').'/kleidooFeedTemp.xml';
		/** INIZIO INTESTAZIONE XML */
		$writer = new \XMLWriter();
		$writer->openURI($uri);
		$writer->startDocument('1.0');
		$writer->setIndent(!$minized);
		$writer->startElement('rss');
		$writer->writeAttribute('xmlns:g', 'http://base.google.com/ns/1.0');
		$writer->writeAttribute('version', '2.0');

		$writer->startElement("channel");
		$writer->writeElement('title', 'Kleidoo Product Feed');
		$writer->writeElement('link', $this->app->baseUrl(false));

		$writer->startElement('author');
		$writer->writeElement('name', 'bambooshoot');
		$writer->endElement();
		$writer->writeElement('description', 'www.pickyshop.com/services/feed/it/kleidoo');
		$writer->writeElement('updated', date(DATE_ATOM,time()));

		/** INIZIO INSERIMENTO PRODOTTI */
		$contoErrori = 0;
		foreach ($products as $pro) {
			try {
				set_time_limit(15);
				$writer->writeRaw($this->kleidooProductToXml($productRepo->findOne($pro),$categories,$helper,$minized));
			}catch(\Exception $e){
				$contoErrori++;
			}
		}
		$writer->endElement();
		$writer->endElement();
		$writer->endDocument();
		$writer->flush();

		$this->log('Report','Run','End build, errors: '.$contoErrori);
	}

	/**
	 * @param AEntity $pro
	 * @param $categories
	 * @param CWidgetHelper $helper
	 * @param $minized
	 * @return string
	 * @throws \redpanda\core\exceptions\RedPandaDBALException
	 */
	public function kleidooProductToXml(AEntity $pro,$categories,CWidgetHelper $helper,$minized)
	{
		$writer = new \XMLWriter();
		$writer->openMemory();
		$writer->setIndent(!$minized);
		$writer->startElement("item");
		$writer->writeElement('g:id', $pro->id . '-' . $pro->productVariantId);
		$writer->startElement('title');
		$writer->writeCdata($pro->productName->getFirst()->name);
		$writer->endElement();

		$writer->startElement('description');
		$writer->writeCdata($pro->productDescription->getFirst()->description);
		$writer->endElement();

		$retrived = $this->app->categoryManager->getCategoriesForProduct($pro->id, $pro->productVariantId);
		$product_type = [];

		$rightCatId = 1;

		foreach($retrived as $category){
			$cats = $this->app->categoryManager->categories()->getPath($category['id']);
			$type = [];
			foreach ($cats as $cat) {
				if($cat['id'] == 1) continue;
				$type[] = $categories[$cat['id']];
			}
			$product_type[] = implode(', ',$type);
			$rightCatId = $category['id'];
		}
		$writer->writeElement('g:product_type', implode('; ', $product_type));
		$rightCat = $this->app->dbAdapter->query("SELECT marketPlaceCategoryId FROM ProductCategoryMarketPlaceLookup where productCategoryId = ?",[$rightCatId])->fetchAll();

		if(isset($rightCat[0])){
			$writer->writeElement('g:google_product_category',$rightCat[0]['marketPlaceCategoryId']);
		}

		$writer->writeElement('link', $pro->getProductUrl($this->app->baseUrl()));
		$writer->writeElement('g:mobile_link', $pro->getProductUrl($this->app->baseUrl()));
		$writer->writeElement('g:image_link', $helper->image($pro->getPhoto(1, 843), 'amazon'));
		for ($i = 2; $i < 15; $i++) {
			$actual = $pro->getPhoto($i, 843);
			if ($actual != false && !empty($actual)) {
				$writer->writeElement('g:additional_image_link', $helper->image($actual));
			}
		}
		$writer->writeElement('g:condition', 'new');
		$sale = 0;
		$writer->startElement('skus');
		foreach ($pro->productSku as $sku) {
			if ($sku->isOnSale) $sale++;
			$writer->startElement('size');
			$writer->writeAttribute('id', $sku->productSizeId);
			$writer->writeElement('name', $sku->productSize->name);
			$writer->writeElement('stock', $sku->stockQty);
			$writer->writeElement('price', $sku->price+15);
			$writer->writeElement('sale_price', $sku->salePrice+15);
			$writer->endElement();
		}
		$writer->endElement();
		$writer->writeElement('on_sale', $sale > 0 ? true : false);
		$writer->writeElement('g:mpn', $pro->itemno . ' ' . $pro->productVariant->name);
		$writer->writeElement('g:brand', $pro->productBrand->name);
		if (isset($pro->productColorGroup) && !$pro->productColorGroup->isEmpty()) {
			$writer->writeElement('g:color', $pro->productColorGroup->getFirst()->name);
		}
		$i = 0;
		foreach ($pro->productAttributeValue as $detail) {
			if (empty($detail->name)) continue;
			$writer->writeElement('g:custom_label_' . $i, $detail->name);
			$i++;
		}

		$writer->endElement();

		return $writer->outputMemory();
	}
}