<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CShooting;
use bamboo\domain\repositories\CDocumentRepo;

/**
 * Class CProductFastCorrelationListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 17/06/2020
 * @since 1.0
 */
class CProductFastLookListAjaxController extends AAjaxController
{
    public function get()
    {

        $productSeason = \Monkey::app()->dbAdapter->query('select max(id) as productSeasonId from ProductSeason ',[])->fetchAll();
        foreach ($productSeason as $val) {
            $productSeasonId = $val['productSeasonId'];
        }
        //$season=\Monkey::app()->router->request()->getRequestData('season');
        if (isset($_REQUEST['season'])) {
            $season = $_REQUEST['season'];
        } else {
            $season = '';
        }
        if (isset($_REQUEST['stored'])) {
            $stored = $_REQUEST['stored'];
        } else {
            $stored = '';
        }
        if (isset($_REQUEST['productShooting'])) {
            $productShooting = $_REQUEST['productShooting'];
        } else {
            $productShooting = '';
        }
        if (isset($_REQUEST['productZeroQuantity'])) {
            $productZeroQuantity = $_REQUEST['productZeroQuantity'];
        } else {
            $productZeroQuantity = '';
        }
        if (isset($_REQUEST['productStatus'])) {
            $productStatus = $_REQUEST['productStatus'];
        } else {
            $productStatus = '';
        }
        if (isset($_REQUEST['productBrandid'])) {
            $productBrandId = $_REQUEST['productBrandid'];
        } else {
            $productBrandId = '';
        }
        if (isset($_REQUEST['productShopid'])) {
            $shopid = $_REQUEST['productShopid'];
        } else {
            $shopid = '';
        }

        if ($season == 1) {
            $sqlFilterSeason = '';
        } else {
            $sqlFilterSeason = ' and p.productSeasonId=' . $productSeasonId;
        }
        if ($productZeroQuantity == 1) {
            $sqlFilterQuantity = '';
        } else {
            $sqlFilterQuantity = 'and p.qty>0';
        }
        if ($productShooting == 1) {
            $sqlFilterShooting = 'and concat(phs.shootingId) is null     ';
        } else {
            $sqlFilterShooting = '';
        }
        if ($productStatus == 1) {
            $sqlFilterStatus = '';
        } else {
            $sqlFilterStatus = 'and p.productStatusId=6';
        }
        if ($productBrandId == 0) {
            $sqlFilterBrand = '';
        } else {
            $sqlFilterBrand = 'and p.productBrandId='.$productBrandId;
        }
        if ($shopid == 0) {
            $sqlFilterShop = '';
        } else {
            $sqlFilterShop = 'and s.id='.$shopid;
        }
        if ($stored == 0) {
            $sqlFilterStored = '';
        } else {
            $sqlFilterStored = 'and p.stored=1';
        }



        $sql = "select  concat(p.id, '-', pv.id)                                                                      AS code,
                  p.id                                                                                              AS id,
                  p.productVariantId                                                                                AS productVariantId,
                  concat(pse.name, ' ', pse.year)                                                               AS season,
                  pse.isActive                                                                                      AS isActive,
                  concat(p.itemno, ' # ', pv.name)                                                              AS cpf,      
                  p.externalId as externalId,  
       
                  pv.description                                                                                    AS colorNameManufacturer,
                  concat(s.id, '-', s.name)                                                                     AS shop,        
                   s.id                                                                                              AS shopId,
                  s.name                                                                                            AS row_shop,
                p.sortingPriorityId                                                                               AS productPriority,
                  pb.name                                                                                           AS brand,
                  ps.name                                                                                           AS status,
                  concat(psg.locale, ' - ',
                         psmg.name)                                                                                 AS productSizeGroup,
                    pl.name as LOOK  
								  

 from Product p 
   JOIN ProductSeason pse ON p.productSeasonId = pse.id
                  JOIN ProductVariant pv ON p.productVariantId = pv.id
                  JOIN ProductBrand pb ON p.productBrandId = pb.id
                  JOIN ProductStatus ps ON ps.id = p.productStatusId
JOIN ShopHasProduct sp ON sp.productId=p.id AND p.productVariantId=sp.productVariantId
  JOIN Shop s ON s.id = sp.shopId
left JOIN ProductHasProductLook ph ON ph.productId=sp.productId AND ph.productVariantId=sp.productVariantId AND ph.shopId=sp.shopId
     left Join ProductLook pl on ph.productLookId=pl.id
     LEFT JOIN (ProductSizeGroup psg
                              JOIN ProductSizeMacroGroup psmg ON psg.productSizeMacroGroupId = psmg.id)
                            ON p.productSizeGroupId = psg.id
where 1=1 " . $sqlFilterSeason . ' ' . $sqlFilterQuantity . ' ' . $sqlFilterStatus . ' ' . $sqlFilterBrand. ' ' . $sqlFilterShop. ' ' . $sqlFilterStored. ' ' . $sqlFilterShooting;


        $shootingCritical = \Monkey::app()->router->request()->getRequestData('shootingCritical');
        if ($shootingCritical) $sql .= " AND `p`.`dummyPicture` not like '%dummy%' AND `p`.`productStatusId` in (4,5,11)";
        $productDetailCritical = \Monkey::app()->router->request()->getRequestData('detailsCritical');
        if ($productDetailCritical) $sql .= " AND `p`.`dummyPicture` not like '%dummy%' AND `p`.`productStatusId` in (4,5,11) HAVING `hasDetails` = 'no'";


        $datatable = new CDataTables($sql,['id','productVariantId'],$_GET,true);
        $shopIds = \Monkey::app()->repoFactory->create('Shop')->getAutorizedShopsIdForUser();
        $datatable->addCondition('shopId',$shopIds);

        $em = $this->app->entityManagerFactory->create('ProductStatus');
        $productStatuses = $em->findAll('limit 99','');

        $statuses = [];
        foreach ($productStatuses as $status) {
            $statuses[$status->code] = $status->name;
        }

        $modifica = $this->app->baseUrl(false) . "/blueseal/friend/prodotti/modifica";
        $okManage = $this->app->getUser()->hasPermission('/admin/product/edit');
        $productRepo = \Monkey::app()->repoFactory->create('Product');
        $productCorrelationRepo=\Monkey::app()->repoFactory->create('ProductLook');
        $productHasProductCorrelationRepo=\Monkey::app()->repoFactory->create('ProductHasProductLook');


        $datatable->doAllTheThings();

        foreach ($datatable->getResponseSetData() as $key => $row) {
            /** @var $val CProduct */
            $val = $productRepo->findOneBy($row);

            $row["DT_RowId"] = $val->printId();
            $row["DT_RowClass"] = $val->productStatus->isVisible == 1 ? 'verde' : (
            $val->productStatus->isReady == 1 ? 'arancione' : ""
            );

            $row['code'] = $okManage ? '<a data-toggle="tooltip" title="modifica" data-placement="right" href="' . $modifica . '?id=' . $val->id . '&productVariantId=' . $val->productVariantId . '">' . $val->id . '-' . $val->productVariantId . '</a>' : $val->id . '-' . $val->productVariantId;
            $row['dummy'] = '<a href="#1" class="enlarge-your-img"><img width="50" src="' . $val->getDummyPictureUrl() . '" /></a>';
            $row['productSizeGroup'] = ($val->productSizeGroup) ? '<span class="small">' . $val->productSizeGroup->locale . '-' . explode("-",$val->productSizeGroup->productSizeMacroGroup->name)[0] . '</span>' : '';

            $row['details'] = "";
            $dummyPicture='';
            $LOOK='';


            $row['dummyPicture']=$val->getDummyPictureUrl();
            $row['hasPhotos'] = ($val->productPhoto->count()) ? 'sì' : 'no';
            $row['dummyVideo'] = ($val->dummyVideo!=null) ? 'sì' : 'no';
            $row['hasDetails'] = (2 < $val->productSheetActual->count()) ? 'sì' : 'no';
            $row['season'] = '<span class="small">' . $val->productSeason->name . " " . $val->productSeason->year . '</span>';

            $row['stock'] = '<table class="nested-table inner-size-table" data-product-id="' . $val->printId() . '"></table>';
            $row['externalId'] = '<span class="small">' . $val->getShopExtenalIds('<br />') . '</span>';

            $row['cpf'] = $val->printCpf();

            $row['colorGroup'] = '<span class="small">' . (!is_null($val->productColorGroup) ? $val->productColorGroup->productColorGroupTranslation->getFirst()->name : "[Non assegnato]") . '</span>';
            $row['brand'] = isset($val->productBrand) ? $val->productBrand->name : "";
            $row['categoryId'] = '<span class="small">' . $val->getLocalizedProductCategories('<br>','/') . '</span>';
            $row['description'] = '<span class="small">' . ($val->productDescriptionTranslation->getFirst() ? $val->productDescriptionTranslation->getFirst()->description : "") . '</span>';

            $row['productName'] = $val->productNameTranslation->getFirst() ? $val->productNameTranslation->getFirst()->name : "";
            $row['tags'] = '<span class="small">' . $val->getLocalizedTags('<br>',false) . '</span>';
            $row['status'] = $val->productStatus->name;
            $row['productPriority'] = $val->sortingPriorityId;

            $qty = 0;
            $shopz = [];
            $mup = [];
            $isOnSale = $val->isOnSale();

            $row['hasQty'] = $qty;
            $row['activePrice'] = $val->getDisplayActivePrice() ? $val->getDisplayActivePrice() : 'Non Assegnato';

            //$row['marketplaces'] = $val->getMarketplaceAccountsName(' - ','<br>',true);

            $row["row_shop"] = $val->getShops('|',true);
            $row['shop'] = '<span class="small">' . $val->getShops('<br />',true) . '</span>';
            $row['shops'] = $val->shopHasProduct->count();


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
                $productCorrelationc = $productCorrelationRepo->findAll();
                foreach($productCorrelationc as $productCorrelation){
                    $findProductCorrelationc=$productHasProductCorrelationRepo->findBy(['productId'=>$shp->productId,'productVariantId'=>$shp->productVariantId,'shopId'=>$shp->shopId,'productLookId'=>$productCorrelation->id]);
                    $correl=$productCorrelation->name;
                    foreach($findProductCorrelationc as $pr){
                            $LOOK.=$correl.':'.$pr->productId.'-'.$pr->productVariantId.'</br>';

                    }

                }




            }

            $row['LOOK']=$LOOK;
            $row['friendPrices'] = implode('<br />',$row['friendPrices']);
            $row['friendValues'] = implode('<br />',$row['friendValues']);
            $row['friendSalePrices'] = implode('<br />',$row['friendSalePrices']);

            $row['colorNameManufacturer'] = $val->productVariant->description;

            $row['isOnSale'] = $val->isOnSale();
            $row['creationDate'] = (new \DateTime($val->creationDate))->format('d-m-Y H:i');





            $datatable->setResponseDataSetRow($key,$row);
        }
        return $datatable->responseOut();
    }
}