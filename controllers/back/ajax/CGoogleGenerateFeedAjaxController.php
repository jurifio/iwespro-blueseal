<?php

namespace bamboo\controllers\back\ajax;


use bamboo\domain\entities\CMarketplaceAccountHasProduct;
use bamboo\domain\entities\CProduct;
use bamboo\domain\repositories\CMarketplaceAccountHasProductRepo;
use bamboo\core\intl\CLang;
use bamboo\core\traits\TMySQLTimestamp;
use bamboo\domain\entities\CMarketplaceAccount;

use bamboo\core\theming\CWidgetHelper;
use const bamboo\ecommerce\offline\feed\AExpertFeedBuilder;

/**
 * Class CAmazonAddProductAjaxControllerController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 11/05/2020
 * @since 1.0
 */
class CGoogleGenerateFeedAjaxController extends AAjaxController
{
    public function get()
    {
        $response = [];
        foreach (\Monkey::app()->repoFactory->create('MarketplaceAccount')->findBy(['marketplaceId' => 2]) as $account) {
            $activeAutomatic = isset($account->config['activeAutomatic']) ? $account->config['activeAutomatic'] : 0;
            $modifier = isset($account->config['priceModifier']) ? $account->config['priceModifier'] : 0;
            $cpc = isset($account->config['defaultCpc']) ? $account->config['defaultCpc'] : 0;
            if ($account->marketplace->type == 'cpc') {
                $response[] = ['id' => $account->printId(),'name' => $account->name,'marketplace' => $account->marketplace->name,'modifier' => $modifier,'cpc' => $cpc,'activeAutomatic' => $activeAutomatic];
            }
        }

        return json_encode($response);
    }

    public function post()
    {
        $this->minized=true;
        $data = \Monkey::app()->router->request()->getRequestData();
        $mp=explode('-',$data['account']);
        $marketplaceAccountId = $mp[0];
        $marketplaceId=$mp[1];
        $marketplaceAccount=\Monkey::app()->repoFactory->create('MarketplaceAccount')->findOneBy(['id'=>$marketplaceAccountId,'marketplaceId'=>$marketplaceId]);
        $langId = $marketplaceAccount->config['lang'];
        $lang = \Monkey::app()->repoFactory->create('Lang')->findOneBy(["lang" => $langId]);
        $this->lang = new CLang($lang->id,$lang->lang);
        unset($lang);
        $this->app->setLang($this->lang);

        $this->marketplaceAccount = \Monkey::app()->repoFactory->create('MarketplaceAccount',$this->lang)->findOne($marketplaceAccount->getIds());
        $this->helper = new CWidgetHelper($this->app);
        $uri = $this->app->rootPath() . $this->app->cfg()->fetch('paths','productSync') . $marketplaceAccount->config['filePath'];


        $writer = $this->createWriter($uri);

        $url = $this->app->baseUrl(false,'https') . $this->marketplaceAccount->config['feedUrl'];

        $writer->startElement('rss');
        $writer->writeAttribute('xmlns:g','http://base.google.com/ns/1.0');
        $writer->writeAttribute('version','2.0');

        $writer->startElement("channel");
        $writer->writeElement('title','Google Product Feed');
        $shopFind=\Monkey::app()->repoFactory->create('Shop')->findOneBy(['id'=>$this->marketplaceAccount->config['shopId']]);
        $shopUrl=$shopFind->urlSite;
        $writer->writeElement('link', $shopUrl);
        //$writer->writeElement('link', $this->app->baseUrl(false, 'https'));

        $writer->startElement('author');
        $writer->writeElement('name', 'iwes');
        $writer->endElement();
        $writer->writeElement('description', $shopUrl);
        $writer->writeElement('updated',date(DATE_ATOM,time()));

        /** INIZIO INSERIMENTO PRODOTTI */
        $contoErrori = $this->writeProductsMinusDeleted($writer);

        $writer->endElement();
        $writer->endElement();
        $writer->endDocument();
        $writer->flush();

        \Monkey::app()->applicationLog('CGoogleGenerateFeedAjaxController','Log','End build','','');
        \Monkey::app()->applicationLog('CGoogleGenerateFeedAjaxController','Log','End build','End build, errors: ' . $contoErrori);
        return 'Bestemmia Errore';
    }
    /**
     * @param $uri
     * @return \XMLWriter
     */
    public function createWriter($uri)
    {
        /** INIZIO INTESTAZIONE XML */
        $writer = new \XMLWriter();
        $writer->openUri($uri);
        $writer->startDocument('1.0');
        $writer->setIndent(!$this->minized);

        return $writer;
    }

    /**
     * @param CProduct|null $product
     * @param CMarketplaceAccountHasProduct|null $marketplaceAccountHasProduct
     * @return string
     */
    public function writeProductEntry(CProduct $product = null,CMarketplaceAccountHasProduct $marketplaceAccountHasProduct = null)
    {
        $writer = new \XMLWriter();
        $writer->openMemory();
        $writer->setIndent(!$this->minized);
        if ($product->qty > 0 ) {
            $writer->startElement("item");
            $writer->writeElement('g:id',$product->printId());

            $avai = 0;
            $sizes = [];
            $onSale = $product->isOnSale();
            foreach ($product->productPublicSku as $sku) {
                $sizes[] = $sku->productSize->name;
                $avai++;
            }

            $writer->startElement('title');
            $variant = ($product->productVariant) ? $product->productVariant->name : '';
            $prodName = $product->getName();
            $name = mb_strtoupper($product->productBrand->name) . ' ' . $variant . ' ' . $prodName;

            if (count($sizes) < 3) $name .= " (" . implode('-',$sizes) . ")";
            $writer->writeCdata($name);
            $writer->endElement();


            $writer->startElement('description');

            $writer->writeCdata($product->getDescription());
            $writer->endElement();


            $product_type = [];
            foreach ($product->productCategory as $category) {
                $cats = $this->app->categoryManager->categories()->getPath($category->id);
                $type = [];
                foreach ($cats as $cat) {
                    if ($cat['id'] == 1) continue;
                    $type[] = \Monkey::app()->repoFactory->create('ProductCategory',$this->lang)->findOne([$cat['id']])->getLocalizedName();
                }
                $product_type[] = implode(', ',$type);
            }

            $writer->writeElement('g:product_type',implode('; ',$product_type));

            $categories = $product->getMarketplaceAccountCategoryIds($this->marketplaceAccount);
            $writer->writeElement('g:google_product_category',$categories[0]);
            // $baseUrlLang = $this->app->cfg()->fetch("paths","domain") . "/" . $this->lang->getLang();
            $shopFind=\Monkey::app()->repoFactory->create('Shop')->findOneBy(['id'=>$this->marketplaceAccount->config['shopId']]);
            $shopUrl=$shopFind->urlSite;
            $baseUrlLang=$shopUrl. "/" . $this->lang->getLang();
            $writer->writeElement('g:link', $product->getProductUrl($baseUrlLang,$this->marketplaceAccount->getCampaignCode()));
            $writer->writeElement('g:mobile_link',$product->getProductUrl($baseUrlLang,$this->marketplaceAccount->getCampaignCode()));
            $writer->writeElement('g:image_link',$this->helper->image($product->getPhoto(1,843),'amazon'));
            for ($i = 2; $i < 8; $i++) {
                $actual = $product->getPhoto($i,843);
                if ($actual != false && !empty($actual)) {
                    $writer->writeElement('g:additional_image_link',$this->helper->image($actual,'amazon'));
                }
            }
            $writer->writeElement('g:condition','new');


            $writer->writeElement('g:availability',$avai > 0 ? 'in stock' : 'out of stock');
            //$writer->writeElement('sizes',implode(';',$sizes));
            //  $writer->writeElement('g:size',implode(';',$sizes));
            $priceActive = \Monkey::app()->repoFactory->create('ProductSku')->findOneBy(['productId' => $product->id,'productVariantId' => $product->productVariantId]);

            $price = number_format($priceActive->price,2,'.','');


            $writer->writeElement('g:price',$price . ' EUR');
            if ($product->isOnSale == 1) {
                $salePrice = number_format($priceActive->salePrice,2,'.','');
                $writer->writeElement('g:sale_price',$salePrice . ' EUR');
            }
            $writer->writeElement('g:mpn',$product->itemno . ' ' . $product->productVariant->name);
            $writer->writeElement('g:brand',$product->productBrand->name);
            if (!is_null($product->productColorGroup)) {
                $writer->writeElement('g:color',$product->productColorGroup->productColorGroupTranslation->getFirst()->name);
            }
            $productEan =  \Monkey::app()->repoFactory->create('ProductEan')->findOneBy(['productId' => $product->id, 'productVariantId' => $product->productVariantId,'used'=>'1']);
            if ($productEan != null) {
                $ean = $productEan->ean;
                $writer->writeElement('g:gtin',$ean);
            }
            $writer->startElement('g:shipping');
            $writer->writeElement('g:service','IT_StandardInternational');
            $writer->writeElement('g:price','10.00 EUR');
            $writer->endElement();
            $writer->startElement('g:shipping');
            $writer->writeElement('g:country','IT');
            $writer->writeElement('g:service','Courier');
            $writer->writeElement('g:price','5.00 EUR');
            $writer->endElement();
            $writer->startElement('g:shipping');
            $writer->writeElement('g:service','IT_ExpeditedInternational');
            $writer->writeElement('g:price','40.00 EUR');


            $writer->endElement();
            $writer->endElement();
        }
        return $writer->outputMemory();

    }
   public function fetchProductsCodeMinusDeleted() {
        $idCycle = $this->app->dbAdapter->query("SELECT concat_ws('-',p.productId,p.productVariantId) AS code
                                                  FROM Product v LEFT JOIN 
                                                        MarketplaceAccountHasProduct p ON v.id = p.productId AND 
                                                                                          v.productVariantId = p.productVariantId AND 
                                                                                          p.marketplaceId = ? AND 
                                                                                          p.marketplaceAccountId = ?
                                                  WHERE ifnull(p.isDeleted, 0) = 0
                                                  GROUP BY p.productId,p.productVariantId", [
            $this->marketplaceAccount->marketplaceId,
            $this->marketplaceAccount->id])->fetchAll(\PDO::FETCH_COLUMN,0);
        return $idCycle;
    }

    /**
     * returns array of "codes" for MarketplaceAccountHasProduct
     */
    public function fetchMarketplaceProduct() {
        $idCycle = $this->app->dbAdapter->query("SELECT concat_ws('-',m.productId,
                                                                    m.productVariantId,
                                                                    m.marketplaceId,
                                                                    m.marketplaceAccountId) as code
                                                FROM MarketplaceAccountHasProduct m, Product p
                                                WHERE   m.productId = p.id and 
                                                        m.productVariantId = p.productVariantId and 
                                                        marketplaceId = ? and 
                                                        marketplaceAccountId = ? and 
                                                        m.isDeleted = 0 
                                                        and p.qty > 0
                                                GROUP BY m.productId,m.productVariantId,m.marketplaceId,m.marketplaceAccountId",
            [$this->marketplaceAccount->marketplaceId,$this->marketplaceAccount->id])->fetchAll(\PDO::FETCH_COLUMN,0);
        return $idCycle;
    }

    /**
     * @param \XMLWriter $writer
     * @return int
     */

    public function writeProductsMinusDeleted(\XMLWriter $writer) {
        $idCycle = $this->fetchProductsCodeMinusDeleted();
        $productRepo = \Monkey::app()->repoFactory->create('Product');
        $contoErrori = 0;
        foreach ($idCycle as $products) {
            $marketplaceAccountHasProduct = null;
            if($products=='') {
                continue;
            }
            $prod=explode('-',$products);
            $product = $productRepo->findOneBy(['id'=>$prod[0],'productVariantId'=>$prod[1]]);
            if ($product == null) {
                continue;
            } else {
                try {
                    set_time_limit(10);
                    /** @var CMarketplaceAccountHasProduct $marketplaceAccountHasProduct */
                    $marketplaceAccountHasProduct = $product->marketplaceAccountHasProduct->findOneByKeys($this->marketplaceAccount->getIds());
                    $writer->writeRaw($this->writeProductEntry($product,$marketplaceAccountHasProduct ? $marketplaceAccountHasProduct : null));

                    if ($marketplaceAccountHasProduct) {
                        if (is_null($marketplaceAccountHasProduct->insertionDate)) {
                            $marketplaceAccountHasProduct->insertionDate = $this->time();
                            $marketplaceAccountHasProduct->publishDate = $this->time();
                            $marketplaceAccountHasProduct->lastRevised = $this->time();
                        }
                        $marketplaceAccountHasProduct->errorRequest = null;
                        $marketplaceAccountHasProduct->isToWork = 0;
                        $marketplaceAccountHasProduct->isRevised = 1;
                        $marketplaceAccountHasProduct->hasError = 0;
                        $marketplaceAccountHasProduct->update();
                    }
                    unset($marketplaceAccountHasProduct);

                } catch (\Throwable $e) {
                    $contoErrori++;
                    if ($marketplaceAccountHasProduct && !is_null($marketplaceAccountHasProduct)) {
                        $marketplaceAccountHasProduct->errorRequest = $e->getMessage() . "\n" . $e->getTraceAsString();
                        $marketplaceAccountHasProduct->isRevised = 0;
                        $marketplaceAccountHasProduct->hasError = 1;
                        $marketplaceAccountHasProduct->update();
                        unset($marketplaceAccountHasProduct);
                    } else {
                        $this->warning('Feed Product ' . $this->marketplaceAccount->printId(),'Error exporting Product: ' . $product->printId(),$e);
                    }
                }
            }
        }
        return $contoErrori;
    }

    public function writeMarketplaceProducts(\XMLWriter $writer) {
        $contoErrori = 0;
        $idCycle = $this->fetchMarketplaceProduct();
        $marketplaceAccountHasProductRepo = \Monkey::app()->repoFactory->create('MarketplaceAccountHasProduct');
        foreach ($idCycle as $marketplaceAccountHasProductId) {
            set_time_limit(5);
            $marketplaceAccountHasProduct = $marketplaceAccountHasProductRepo->findOneByStringId($marketplaceAccountHasProductId);
            if(is_null($marketplaceAccountHasProduct)) {
                $this->error('writeMarketplaceProducts','marketplaceAccountHasProduct not found while it should be there',$marketplaceAccountHasProductId);
                continue;
            }
            try {
                $writer->writeRaw($this->writeProductEntry($marketplaceAccountHasProduct->product,$marketplaceAccountHasProduct));
                if (is_null($marketplaceAccountHasProduct->insertionDate)) {
                    $marketplaceAccountHasProduct->insertionDate = $this->time();
                    $marketplaceAccountHasProduct->publishDate = $this->time();
                    $marketplaceAccountHasProduct->lastRevised = $this->time();
                }
                $marketplaceAccountHasProduct->isToWork = 0;
                $marketplaceAccountHasProduct->isRevised = 1;
                $marketplaceAccountHasProduct->hasError = 0;
                $marketplaceAccountHasProduct->update();
                unset($marketplaceAccountHasProduct);
            } catch (\Throwable $e) {
                $contoErrori++;
                $marketplaceAccountHasProduct->errorRequest = $e->getMessage() . "\n" . $e->getTraceAsString();
                $marketplaceAccountHasProduct->isRevised = 0;
                $marketplaceAccountHasProduct->hasError = 1;
                $marketplaceAccountHasProduct->update();
            }
        }
        return $contoErrori;
    }
}