<?php

namespace bamboo\blueseal\jobs;


use PDO;
use prepare;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CProductPublicSku;
use bamboo\domain\entities\CProduct;
use bamboo\core\events\AEventListener;
use bamboo\domain\repositories\CMarketplaceAccountHasProductRepo;
use bamboo\domain\repositories\CProductRepo;




/**
 * Class CPublishProductIntoMarketplaceJob
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 16/01/2020
 * @since 1.0
 */
class CPublishProductIntoMarketplaceJob extends ACronJob
{
    /**
     * @param null $args
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function run($args = null)
    {

        set_time_limit(0);
        ini_set('memory_limit','2048M');

        $res = "";
        /********marketplace********/
        $db_host = "5.189.159.187";
        $db_name = "iwesPrestaDB";
        $db_user = "pickyshop4";
        $db_pass = "rrtYvg6W!";
        try {

            $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
            $db_con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $res .= " connessione ok <br>";
        } catch (PDOException $e) {
            $res .= $e->getMessage();
        }
        $marketplaceAccountRepo = \Monkey::app()->repoFactory->create('MarketplaceAccount');
        $productBrandRepo = \Monkey::app()->repoFactory->create('ProductBrand');
        /** @var CProductRepo $productRepo */
        $productRepo = \Monkey::app()->repoFactory->create('Product');
        $productSkuRepo = \Monkey::app()->repoFactory->create('ProductSku');
        $marketplaceRepo = \Monkey::app()->repoFactory->create('Marketplace');

        $marketplaceAccounts = $marketplaceAccountRepo->findBy(['id' => 32,'marketplaceId' => 9]);

        foreach ($marketplaceAccounts as $marketplaceAccount) {
            try {
                $markeplaceId = $marketplaceAccount->marketplaceId;
                $marketplaceAccountId = $marketplaceAccount->id;
                $activeAutomatic = isset($marketplaceAccount->config['activeAutomatic']) ? $marketplaceAccount->config['activeAutomatic'] : 0;

                $isActive = isset($marketplaceAccount->config['isActive']) ? $marketplaceAccount->config['isActive'] : 0;
                if ($isActive == 0) {
                    continue;
                }
                $rows = [];
                $countProduct = 0;
                $bodyMail = '<html><body>Elenco Prodotti in Pubblicazione per aggregatore ' . $marketplaceAccount->name . ':<br><table><thead><th>codice</th><th>brand</th><th>categoria</th><th>cpc Desktop</th><th>cpc Mobile</th></thead>';
                $marketplace = $marketplaceRepo->findOneBy(['id' => $marketplaceAccount->marketplaceId]);
                $priceRange1 = explode('-',$marketplaceAccount->config['priceModifierRange1']);
                $priceRange2 = explode('-',$marketplaceAccount->config['priceModifierRange2']);
                $priceRange3 = explode('-',$marketplaceAccount->config['priceModifierRange3']);
                $priceRange4 = explode('-',$marketplaceAccount->config['priceModifierRange4']);
                $priceRange5 = explode('-',$marketplaceAccount->config['priceModifierRange5']);


                $filters = explode(',',json_encode($marketplaceAccount->config['ruleOption'],false));
                foreach ($filters as $filter) {
                    $brandShop = explode('-',$filter);
                    $brand = $brandShop[0];
                    $shopD = $brandShop[1];
                    $productBrand = $productBrandRepo->findOneBy(['id' => $brand]);
                    $brandName = $productBrand->name;

                    $products = $productRepo->findBy(['productBrandId' => $brand,'productStatusId' => 6]);

                    foreach ($products as $product) {
                        $isOnSale = $product->isOnSale;
                        if ($product->qty >= 1) {
                            $productSku = $productSkuRepo->findOneBy(['productId' => $product->id,'productVariantId' => $product->productVariantId,'shopId' => $shopD]);
                            if ($productSku != null) {
                                if ($marketplace->type == 'cpc') {
                                    if ($activeAutomatic == 0) {
                                        $fee = $marketplaceAccount->config['defaultCpc'];
                                        $feeMobile = $marketplaceAccount->config['defaultCpcM'];
                                    } else {
                                        $price = $productSku->price;
                                        $salePrice = $productSku->salePrice;
                                        if ($isOnSale == 1) {
                                            $activePrice = $salePrice;
                                        } else {
                                            $activePrice = $price;
                                        }


                                        switch (true) {
                                            case $activePrice >= $priceRange1[0] && $activePrice <= $priceRange1[1]:
                                                $fee = $marketplaceAccount->config['range1Cpc'];
                                                $feeMobile = $marketplaceAccount->config['range1CpcM'];
                                                break;
                                            case $activePrice >= $priceRange2[0] && $activePrice <= $priceRange2[1]:
                                                $fee = $marketplaceAccount->config['range2Cpc'];
                                                $feeMobile = $marketplaceAccount->config['range2CpcM'];
                                                break;
                                            case $activePrice >= $priceRange3[0] && $activePrice <= $priceRange3[1]:
                                                $fee = $marketplaceAccount->config['range3Cpc'];
                                                $feeMobile = $marketplaceAccount->config['range3CpcM'];
                                                break;
                                            case $activePrice >= $priceRange4[0] && $activePrice <= $priceRange4[1]:
                                                $fee = $marketplaceAccount->config['range4Cpc'];
                                                $feeMobile = $marketplaceAccount->config['range4CpcM'];

                                                break;
                                            case $activePrice >= $priceRange5[0] && $activePrice <= $priceRange5[1]:
                                                $fee = $marketplaceAccount->config['range5Cpc'];
                                                $feeMobile = $marketplaceAccount->config['range5CpcM'];
                                                break;
                                        }
                                    }
                                } else {
                                    $fee = 'nessun cpc ';
                                    $feeMobile = 'nessun cpc Mobile';
                                }
                                $countProduct++;
                                array_push($rows,[$productSku->productId . '-' . $product->productVariantId]);
                                $bodyMail .= " <tr><td>" . $productSku->productId . '-' . $productSku->productVariantId . ' </td><td>' . $brandName . '</td><td>' . $product->getLocalizedProductCategories('/','/') . ' </td><td>' . $fee . ' </td><td>' . $feeMobile . '</td></tr>';
                            }

                        } else {
                            continue;
                        }
                    }

                }
                /** @var CMarketplaceAccountHasProductRepo $marketplaceAccountHasProductRepo */
                $marketplaceAccountHasProductRepo = \Monkey::app()->repoFactory->create('MarketplaceAccountHasProduct');
                \Monkey::app()->repoFactory->beginTransaction();
                foreach ($rows as $row) {
                    try {
                        $ids = [];
                        set_time_limit(6);
                        $product = $productRepo->findOneByStringId($row);
                        $marketplaceAccountHasProduct = $marketplaceAccountHasProductRepo->addProductToMarketplaceAccountJob($product,$marketplaceAccount,$activeAutomatic);
                        $i++;
                        \Monkey::app()->repoFactory->commit();
                    } catch
                    (\Throwable $e) {
                        \Monkey::app()->repoFactory->rollback();
                        throw $e;
                    }
                }
                $bodyMail.='</table></body></html>';
                $bodyMail .= 'numero Prodotti Totali in Coda di Pubblicazione ' . $countProduct;
                /** @var CEmailRepo $mailRepo */
                $mailRepo = \Monkey::app()->repoFactory->create('Email');
                $mailRepo->newMail('it@iwes.it',["gianluca@iwes.it","juri@iwes.it"],[],[],"coda Pubblicazione su " . $marketplaceAccount->name,$bodyMail);
                $aggregatorPublishLog=\Monkey::app()->repoFactory->create('AggregatorPublishLog')->getEmptyEntity();
                $aggregatorPublishLog->marketplaceAccountId=$markeplaceAccountId;
                $aggregatorPublishLog->marketplaceId=$markeplaceId;
                $aggregatorPublishLog->subject="Pubblicazione su " . $marketplaceAccount->name;
                $aggregatorPublishLog->result="success";
                $aggregatorPublishLog->email="gianluca@iwes.it,juri@iwes.it";
                $aggregatorPublishLog->insert();

            }catch(\Throwable $e){
                $bodyMail='<html><body>errore nella mail<br>'.$e.'</body></html>';
                $mailRepo = \Monkey::app()->repoFactory->create('Email');
                $mailRepo->newMail('it@iwes.it',["gianluca@iwes.it","juri@iwes.it"],[],[],"coda Pubblicazione su " . $marketplaceAccount->name,$bodyMail);
                $aggregatorPublishLog=\Monkey::app()->repoFactory->create('AggregatorPublishLog')->getEmptyEntity();
                $aggregatorPublishLog->marketplaceAccountId=$markeplaceAccountId;
                $aggregatorPublishLog->marketplaceId=$markeplaceId;
                $aggregatorPublishLog->subject="Pubblicazione su " . $marketplaceAccount->name;
                $aggregatorPublishLog->result="error";
                $aggregatorPublishLog->email="gianluca@iwes.it,juri@iwes.it";
                $aggregatorPublishLog->insert();
            }
        }
    }


}