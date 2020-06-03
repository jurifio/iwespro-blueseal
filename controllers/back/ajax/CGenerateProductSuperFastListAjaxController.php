<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooOrderLineException;
use bamboo\core\exceptions\BambooShipmentException;
use bamboo\domain\entities\COrderLine;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CShooting;
use bamboo\domain\repositories\CDocumentRepo;
use bamboo\domain\repositories\COrderLineRepo;
use bamboo\domain\repositories\CShipmentRepo;
use bamboo\utils\time\STimeToolbox;
use DateTime;
use PDO;
use PDOException;

/**
 * Class CGenerateProductSuperFastListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 03/06/2020
 * @since 1.0
 */
class CGenerateProductSuperFastListAjaxController extends AAjaxController
{
    public function post()
    {
        $lastExecution=\Monkey::app()->repoFactory->create('Job')->findOneBy(['id'=>146])->lastExecution;
        $today = new \DateTime();
        $now = $today->format('Y-m-d H:i:s');

       /* $sql = "SELECT
                  concat(p.id, '-', pv.id)                                                                      AS code,
                  p.id                                                                                              AS id,
                  p.productVariantId                                                                                AS productVariantId,
                  concat(pse.name, ' ', pse.year)                                                               AS season,
                  pse.isActive                                                                                      AS isActive,
                  concat(p.itemno, ' # ', pv.name)                                                              AS cpf,
                  pv.description                                                                                    AS colorNameManufacturer,
                  pb.name                                                                                           AS brand,
                  ps.name                                                                                           AS status,  
                  p.creationDate                                                                                    AS creationDate,
                  p.lastUpdate as lastUpdate,  
                  if((isnull(p.dummyPicture) OR (p.dummyPicture = 'bs-dummy-16-9.png')), 'no', 'sì')            AS dummy,
                  p.isOnSale                                                                                        AS isOnSale,
                  p.qty                                                                                             AS hasQty
                
                FROM Product p
                  JOIN ProductSeason pse ON p.productSeasonId = pse.id
                  JOIN ProductVariant pv ON p.productVariantId = pv.id
                  JOIN ProductBrand pb ON p.productBrandId = pb.id
                  JOIN ProductStatus ps ON ps.id = p.productStatusId
                 WHERE 1=1 and  p.lastUpdate>='".$lastExecution."' and p.lastUpdate<='".$now."' GROUP BY p.id,p.productVariantId  ";*/
        $sql = "SELECT
                  concat(p.id, '-', pv.id)                                                                      AS code,
                  p.id                                                                                              AS id,
                  p.productVariantId                                                                                AS productVariantId,
                  concat(pse.name, ' ', pse.year)                                                               AS season,
                  pse.isActive                                                                                      AS isActive,
                  concat(p.itemno, ' # ', pv.name)                                                              AS cpf,
                  pv.description                                                                                    AS colorNameManufacturer,
                  pb.name                                                                                           AS brand,
                  ps.name                                                                                           AS status,  
                  p.creationDate                                                                                    AS creationDate,
                  p.lastUpdate as lastUpdate,  
                  if((isnull(p.dummyPicture) OR (p.dummyPicture = 'bs-dummy-16-9.png')), 'no', 'sì')            AS dummy,
                  p.isOnSale                                                                                        AS isOnSale,
                  p.qty                                                                                             AS hasQty
                
                FROM Product p
                  JOIN ProductSeason pse ON p.productSeasonId = pse.id
                  JOIN ProductVariant pv ON p.productVariantId = pv.id
                  JOIN ProductBrand pb ON p.productBrandId = pb.id
                  JOIN ProductStatus ps ON ps.id = p.productStatusId
                  GROUP BY p.id,p.productVariantId  ";
        $em = $this->app->entityManagerFactory->create('ProductStatus');
        $productStatuses = $em->findAll('limit 99','');

        $statuses = [];
        foreach ($productStatuses as $status) {
            $statuses[$status->code] = $status->name;
        }
        /** @var CDocumentRepo $docRepo */
        $docRepo = \Monkey::app()->repoFactory->create('Document');
        $modifica = $this->app->baseUrl(false) . "/blueseal/friend/prodotti/modifica";
        $row = [];
        $productRepo = \Monkey::app()->repoFactory->create('Product');
        $productViewRepo = \Monkey::app()->repoFactory->create('ProductView');
        $productSheetActualRepo = \Monkey::app()->repoFactory->create('ProductSheetActual');
        $productDetailTranslationRepo = \Monkey::app()->repoFactory->create('ProductDetailTranslation');
        $productNameTranslationRepo = \Monkey::app()->repoFactory->create('ProductNameTranslation');
        $res = \Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
        foreach ($res as $result) {
            $findProductView = $productViewRepo->findOneBy(['productId'=> $result['id'],'productVariantId' => $result['productVariantId']]);
            if ($findProductView == null) {
                $productViewInsert=$productViewRepo->getEmptyEntity();
                /** @var $val CProduct */
                $val = $productRepo->findOneBy(['id' => $result['id'],'productVariantId' => $result['productVariantId']]);

                $productViewInsert->productId = $val->id;
                $productViewInsert->productVariantId = $val->productVariantId;

                $productViewInsert->dummy = '<a href="#1" class="enlarge-your-img"><img width="50" src="' . $val->getDummyPictureUrl() . '" /></a>';
                $productViewInsert->productSizeGroup = ($val->productSizeGroup) ?  $val->productSizeGroup->locale . '-' . explode("-",$val->productSizeGroup->productSizeMacroGroup->name)[0]  : '';

                $details = "";
                foreach ($val->productSheetActual as $k => $v) {
                    if (!is_null($v->productDetail) && !$v->productDetail->productDetailTranslation->isEmpty()) {
                        $details .=  $v->productDetail->productDetailTranslation->getFirst()->name . "<br />";
                    }
                }
                $productViewInsert->details = $details;

                $productViewInsert->hasPhotos = ($val->productPhoto->count()) ? 'sì' : 'no';
                $productViewInsert->hasDetails = (2 < $val->productSheetActual->count()) ? 'sì' : 'no';
                $productViewInsert->season = $val->productSeason->name . " " . $val->productSeason->year ;

                $productViewInsert->stock = '<table class="nested-table inner-size-table" data-product-id="' . $val->printId() . '"></table>';
                $productViewInsert->externalId =  $val->getShopExtenalIds('<br />') ;

                $productViewInsert->cpf = $val->printCpf();

                $productViewInsert->colorGroup =  (!is_null($val->productColorGroup) ? $val->productColorGroup->productColorGroupTranslation->getFirst()->name : "[Non assegnato]") ;
                $productViewInsert->brand = isset($val->productBrand) ? $val->productBrand->name : "";
                $productViewInsert->categoryId =  $val->getLocalizedProductCategories('<br>','/');
                $productViewInsert->description = ($val->productDescriptionTranslation->getFirst() ? $val->productDescriptionTranslation->getFirst()->description : "");

                $productViewInsert->productName = $val->productNameTranslation->getFirst() ? $val->productNameTranslation->getFirst()->name : "";
                $productViewInsert->tags =  $val->getLocalizedTags('<br>',false) ;
                $productViewInsert->status = $val->productStatus->name;
                $productViewInsert->productPriority = $val->sortingPriorityId;
                $productViewInsert->lastUpdate = $val->lastUpdate;

                $qty = 0;
                $shopz = [];
                $mup = [];
                $isOnSale = $val->isOnSale();
                foreach ($val->productSku as $sku) {
                    $qty += $sku->stockQty;
                    $iShop = $sku->shop->name;
                    if (!in_array($iShop,$shopz)) {
                        $shopz[] = $iShop;

                        $price = $isOnSale ? $sku->salePrice : $sku->price;

                        if ((float)$price) {
                            $multiplier = ($val->productSeason->isActive) ? (($isOnSale) ? $sku->shop->saleMultiplier : $sku->shop->currentSeasonMultiplier) : $sku->shop->pastSeasonMultiplier;
                            $value = $sku->value;
                            $friendRevenue = $value + $value * $multiplier / 100;
                            $priceNoVat = $price / 1.22;
                            $mup[] = number_format(($priceNoVat - $friendRevenue) / $priceNoVat * 100,2,",",".");
                        } else {
                            $mup[] = '-';
                        }
                    }
                }
                $productViewInsert->hasQty = $qty;
                $productViewInsert->activePrice = $val->getDisplayActivePrice() ? $val->getDisplayActivePrice() : 'Non Assegnato';

                //$row['marketplaces'] = $val->getMarketplaceAccountsName(' - ','<br>',true);
                $productViewInsert->marketplaces = "";
                $productViewInsert->shop =  $val->getShops('<br />',true) ;
                $productViewInsert->shops = $val->shopHasProduct->count();


                //$row['mup'] = '<span class="small">';
                //$row['mup'] .= implode('<br />', $mup);
                //$row['mup'] .= '</span>';

                $row['friendPrices'] = [];
                $row['friendValues'] = [];
                $row['friendSalePrices'] = [];
                foreach ($val->shopHasProduct as $shp) {
                    $row['friendPrices'][] = $shp->price;
                    $row['friendValues'][] = $shp->value;
                    $row['friendSalePrices'][] = $shp->salePrice;
                }

                $productViewInsert->friendPrices = implode('<br />',$row['friendPrices']);
                $productViewInsert->friendValues = implode('<br />',$row['friendValues']);
                $productViewInsert->friendSalePrices = implode('<br />',$row['friendSalePrices']);

                $productViewInsert->colorNameManufacturer = $val->productVariant->description;

                $productViewInsert->isOnSale = $val->isOnSale();
                $productViewInsert->creationDate = (new \DateTime($val->creationDate))->format('d-m-Y H:i');
                $productViewInsert->processing = ($val->processing) ? $val->processing : '-';

                $sids = "";
                $ddtNumbers = "";
                /** @var CShooting $singleShooting */
                foreach ($val->shooting as $singleShooting) {
                    $sids .= '<br />' . $singleShooting->id;
                    $ddtNumbers .= '<br />' . $docRepo->findShootingFriendDdt($singleShooting);
                }
                $productViewInsert->shooting = $sids;
                $productViewInsert->doc_number = $ddtNumbers;
                $productViewInsert->inPrestashop = is_null($val->prestashopHasProduct) ? 'no' : 'si';
                $productViewInsert->insert();
            } else {
                // $lastUpdate=(new \DateTime($result[$lastUpdate]))->format('d-m-Y H:i');


                $lastUpdate = strtotime($findProductView->lastUpdate);
                $currentUpdate = strtotime($result['lastUpdate']);
                if ($currentUpdate > $lastUpdate) {
                    /** @var $val CProduct */
                    $val = $productRepo->findOneBy(['id' => $result['id'],'productVariantId' => $result['productVariantId']]);

                    $productView->dummy = '<a href="#1" class="enlarge-your-img"><img width="50" src="' . $val->getDummyPictureUrl() . '" /></a>';
                    $productView->productSizeGroup = ($val->productSizeGroup) ?  $val->productSizeGroup->locale . '-' . explode("-",$val->productSizeGroup->productSizeMacroGroup->name)[0]  : '';

                    $details = "";
                    foreach ($val->productSheetActual as $k => $v) {
                        if (!is_null($v->productDetail) && !$v->productDetail->productDetailTranslation->isEmpty()) {
                            $details .=  $v->productDetail->productDetailTranslation->getFirst()->name . "<br />";
                        }
                    }
                    $productView->details = $details;

                    $productView->hasPhotos = ($val->productPhoto->count()) ? 'sì' : 'no';
                    $productView->hasDetails = (2 < $val->productSheetActual->count()) ? 'sì' : 'no';
                    $productView->season =  $val->productSeason->name . " " . $val->productSeason->year ;

                    $productView->stock = '<table class="nested-table inner-size-table" data-product-id="' . $val->printId() . '"></table>';
                    $productView->externalId =  $val->getShopExtenalIds('<br />') ;

                    $productView->cpf = $val->printCpf();

                    $productView->colorGroup =  (!is_null($val->productColorGroup) ? $val->productColorGroup->productColorGroupTranslation->getFirst()->name : "[Non assegnato]") ;
                    $productView->brand = isset($val->productBrand) ? $val->productBrand->name : "";
                    $productView->categoryId =  $val->getLocalizedProductCategories('<br>','/') ;
                    $productView->description =  ($val->productDescriptionTranslation->getFirst() ? $val->productDescriptionTranslation->getFirst()->description : "") ;

                    $productView->productName = $val->productNameTranslation->getFirst() ? $val->productNameTranslation->getFirst()->name : "";
                    $productView->tags =  $val->getLocalizedTags('<br>',false) ;
                    $productView->status = $val->productStatus->name;
                    $productView->productPriority = $val->sortingPriorityId;
                    $productView->lastUpdate = $val->lastUpdate;

                    $qty = 0;
                    $shopz = [];
                    $mup = [];
                    $isOnSale = $val->isOnSale();
                    foreach ($val->productSku as $sku) {
                        $qty += $sku->stockQty;
                        $iShop = $sku->shop->name;
                        if (!in_array($iShop,$shopz)) {
                            $shopz[] = $iShop;

                            $price = $isOnSale ? $sku->salePrice : $sku->price;

                            if ((float)$price) {
                                $multiplier = ($val->productSeason->isActive) ? (($isOnSale) ? $sku->shop->saleMultiplier : $sku->shop->currentSeasonMultiplier) : $sku->shop->pastSeasonMultiplier;
                                $value = $sku->value;
                                $friendRevenue = $value + $value * $multiplier / 100;
                                $priceNoVat = $price / 1.22;
                                $mup[] = number_format(($priceNoVat - $friendRevenue) / $priceNoVat * 100,2,",",".");
                            } else {
                                $mup[] = '-';
                            }
                        }
                    }
                    $productView->hasQty = $qty;
                    $productView->activePrice = $val->getDisplayActivePrice() ? $val->getDisplayActivePrice() : 'Non Assegnato';

                    //$row['marketplaces'] = $val->getMarketplaceAccountsName(' - ','<br>',true);
                    $productView->marketplaces = "";
                    $productView->shop =  $val->getShops('<br />',true) ;
                    $productView->shops = $val->shopHasProduct->count();


                    //$row['mup'] = '<span class="small">';
                    //$row['mup'] .= implode('<br />', $mup);
                    //$row['mup'] .= '</span>';

                    $row['friendPrices'] = [];
                    $row['friendValues'] = [];
                    $row['friendSalePrices'] = [];
                    foreach ($val->shopHasProduct as $shp) {
                        $row['friendPrices'][] = $shp->price;
                        $row['friendValues'][] = $shp->value;
                        $row['friendSalePrices'][] = $shp->salePrice;
                    }

                    $productView->friendPrices = implode('<br />',$row['friendPrices']);
                    $productView->friendValues = implode('<br />',$row['friendValues']);
                    $productView->friendSalePrices = implode('<br />',$row['friendSalePrices']);

                    $productView->colorNameManufacturer = $val->productVariant->description;

                    $productView->isOnSale = $val->isOnSale();
                    $productView->creationDate = (new \DateTime($val->creationDate))->format('d-m-Y H:i');
                    $productView->processing = ($val->processing) ? $val->processing : '-';

                    $sids = "";
                    $ddtNumbers = "";
                    /** @var CShooting $singleShooting */
                    foreach ($val->shooting as $singleShooting) {
                        $sids .= '<br />' . $singleShooting->id;
                        $ddtNumbers .= '<br />' . $docRepo->findShootingFriendDdt($singleShooting);
                    }
                    $productView->shooting = $sids;
                    $productView->doc_number = $ddtNumbers;
                    $productView->inPrestashop = is_null($val->prestashopHasProduct) ? 'no' : 'si';
                    $productView->update();

                }

            }


        }
    return 'ok';
    }
}