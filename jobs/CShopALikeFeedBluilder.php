<?php
namespace bamboo\blueseal\jobs;

use bamboo\domain\entities\CProduct;
use bamboo\core\db\pandaorm\entities\AEntity;
use bamboo\core\jobs\ACronJob;
use bamboo\core\theming\CWidgetHelper;

/**
 * Class CDispatchPreorderToFriend
 * @package bamboo\blueseal\jobs
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CShopALikeFeedBluilder extends ACronJob
{
	protected $marketCat = [];

	public function run($args = null)
	{
		$this->log('Report', 'Run', 'Starting To build');
		$minized = $args == 'minified';
		/** PREPARAZIONE */
		/** @var CCategoryManager $cm */

		$cm = $this->app->categoryManager;
		$helper = new CWidgetHelper($this->app);
		$categoriesName = $this->app->dbAdapter->query("SELECT productCategoryId, name FROM ProductCategoryHasLang WHERE langId = ?", [$this->app->getLang()->getId()])->fetchAll();
		$categories = [];
		foreach ($categoriesName as $alls) {
			$categories[$alls['productCategoryId']] = $alls['name'];
		}
		unset($categoriesName);

		$rightCat = $this->app->dbAdapter->query("SELECT productCategoryId, marketPlaceCategoryId FROM ProductCategoryMarketPlaceLookup", [])->fetchAll();

		foreach ($rightCat as $mc) {
			$this->marketCat[$mc['productCategoryId']] = $mc['marketPlaceCategoryId'];
		}

		/** TROVO PRODOTTI */
		$repo = $this->app->repoFactory->create('Product');
		$products = $this->app->dbAdapter->query("SELECT DISTINCT product AS id, variant AS productVariantId FROM vProductSortingView", [])->fetchAll();


		$uri = $this->app->cfg()->fetch('paths', 'tempFolder') . '/shopALikeFeedTemp.xml';

		/** INIZIO INTESTAZIONE XML */
		$writer = new \XMLWriter();

		$writer->openURI($uri);
		$writer->startDocument('1.0', 'UTF-8');
		$writer->setIndent(!$minized);
		$writer->startElement('rss');
		$writer->writeAttribute('xmlns:g', 'http://base.google.com/ns/1.0');
		$writer->writeAttribute('version', '2.0');

		$writer->startElement("channel");
		$writer->writeElement('title', 'ShopALike Product Feed');
		$writer->writeElement('link', $this->app->baseUrl(false));

		$writer->startElement('author');
		$writer->writeElement('name', 'bambooshoot');
		$writer->endElement();
		$writer->writeElement('description', 'https://www.pickyshop.com/services/feed/it/shopalike');
		$writer->writeElement('updated', date(DATE_ATOM, time()));

		/** INIZIO INSERIMENTO PRODOTTI */
		$contoErrori = 0;
		foreach ($products as $pro) {
			try {
				set_time_limit(5);
				$writer->writeRaw($this->shopALikeProductToXML($repo->findOne($pro), $categories, $helper, $minized));
			} catch (\Exception $e) {
				$contoErrori++;
			}
		}
		$writer->endElement();
		$writer->endElement();
		$writer->endDocument();
		$writer->flush();

		$this->log('Report', 'Run', 'End build, errors: ' . $contoErrori);
	}

	/**
	 * @param CProduct $product
	 * @param array $categories
	 * @param CWidgetHelper $helper
	 * @param bool $minized
	 * @return string
	 * @throws \bamboo\core\exceptions\RedPandaDBALException
	 */
	public function shopALikeProductToXML(CProduct $product, array $categories, CWidgetHelper $helper,$minized = true)
	{
		$writer = new \XMLWriter();
		$writer->openMemory();
		$writer->setIndent(!$minized);
		$writer->startElement("item");
		$writer->writeElement('g:id', $product->id . '-' . $product->productVariantId);
		$writer->startElement('title');
		if(empty($product->productName->getFirst()->name)){
			$name = mb_strtoupper($product->productBrand->name).' '.$product->itemno;
		} else {
			$name = mb_strtoupper($product->productBrand->name).' '.$product->productName->getFirst()->name;
		}
		$writer->writeCdata($name);
		$writer->endElement();

		$writer->startElement('description');
		$writer->writeCdata($product->productDescription->getFirst()->description);
		$writer->endElement();


		$product_type = [];

		$rightCatId = 1;

		foreach($product->productCategory as $category){
			$cats = $this->app->categoryManager->categories()->getPath($category->id);
			$type = [];
			foreach ($cats as $cat) {
				if($cat['id'] == 1) continue;
				$type[] = $categories[$cat['id']];
			}
			$product_type[] = implode(', ',$type);
			$rightCatId = $category->id;
		}
		$writer->writeElement('g:product_type', implode('; ', $product_type));

		if(isset($this->marketCat[$rightCatId])){
			$writer->writeElement('g:google_product_category',$this->marketCat[$rightCatId]);
		}

		$writer->writeElement('g:link', $product->getProductUrl($this->app->baseUrl()));
		$writer->writeElement('g:mobile_link', $product->getProductUrl($this->app->baseUrl()));
		$writer->writeElement('g:image_link', $helper->image($product->getPhoto(1, 843), 'amazon'));
		for ($i = 2; $i < 8; $i++) {
			$actual = $product->getPhoto($i, 843);
			if ($actual!= false && !empty($actual)) {
				$writer->writeElement('g:additional_image_link', $helper->image($actual,'amazon'));
			}
		}
		$writer->writeElement('g:condition', 'new');
		$avai = 0;
		$product->price = 0;
		$product->sale_price = 0;
		$sizes = [];
		$onSale = false;
		foreach ($product->productSku as $sku) {
			if(!$onSale && $sku->isOnSale == 1) $onSale = true;
			if ($sku->price > $product->price) {
				$product->price = $sku->price;
			}
			if ($sku->salePrice > $product->sale_price) {
				$product->sale_price = $sku->salePrice;
			}
			if ($sku->stockQty > 0) {
				$sizes[] = $sku->productSize->name;
				$avai++;
			}
		}

		$writer->writeElement('g:availability', $avai > 0 ? 'in stock' : 'out of stock');
		$writer->writeElement('sizes',implode(',',$sizes));
		$writer->writeElement('g:price', $product->price);
		if($onSale){
			$writer->writeElement('g:sale_price', $product->sale_price);
		} else {
			$writer->writeElement('g:sale_price', $product->price);
		}
		$writer->writeElement('g:mpn', $product->itemno . ' ' . $product->productVariant->name);
		$writer->writeElement('g:brand', $product->productBrand->name);
		if (isset($product->productColorGroup) && !$product->productColorGroup->isEmpty()) {
			$writer->writeElement('g:color', $product->productColorGroup->getFirst()->name);
		}
		$writer->startElement('g:shipping');
		$writer->writeElement('g:service', 'Courier');
		$writer->writeElement('g:price', '10.00 EUR');
		$writer->endElement();
		$writer->startElement('g:shipping');
		$writer->writeElement('g:country', 'IT');
		$writer->writeElement('g:service', 'Courier');
		$writer->writeElement('g:price', '0.00 EUR');
		$writer->endElement();
		$writer->endElement();
		return $writer->outputMemory();
	}

}