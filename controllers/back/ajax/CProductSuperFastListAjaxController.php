<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CShooting;
use bamboo\domain\repositories\CDocumentRepo;

/**
 * Class CProductShopListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 30/05/2020
 * @since 1.0
 */
class CProductSuperFastListAjaxController extends AAjaxController
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

        if ($season == 1) {
            $sqlFilterSeason = '';
        } else {
            $sqlFilterSeason = ' and p.productSeasonId=' . $productSeasonId;
        }
        if ($productZeroQuantity == 1) {
            $sqlFilterQuantity = '';
        } else {
            $sqlFilterQuantity = 'and p.hasQty>0';
        }
        if ($productStatus == 1) {
            $sqlFilterStatus = '';
        } else {
            $sqlFilterStatus = 'and p.productStatusId=6';
        }


        $sql = "SELECT
                  concat(p.id, '-', p.productVariantId)                                                                      AS code,
                  p.id                                                                                              AS id,
                  p.shop as shop,
                  p.colorGroup as colorGroup,
                  p.colorNameManufacturer as colorNameManufacturer,
                  p.season as season,
                  p.externalId as externalId,
                  p.cpf as cpf,
                  p.details as details,
                  p.dummy as dummy,
                  p.hasPhotos as hasPhotos,
                  p.productName as productName,  
                  p.hasDetails as hasDetails,
                  p.brand as brand,
                  p.productSizeGroup as productSizeGroup,
                  p.categoryId as categoryId,
                  p.tags as tags,
                  p.status as status,
                  p.hasQty as hasQty,
                  p.isOnSale as isOnSale,
                  p.productPriority as ProductPriority,
                  p.description as description,         
                  p.marketplaces as marketplaces,
                  p.stock as stock,
                  p.activePrice as activePrice,
                  p.shops as shops,
                  p.friendPrices as friendPrices,  
                  p.friendValues as friendValues,
                  p.processing as processing,
                  p.shooting as shooting,
                  p.doc_number as doc_number,
                  p.inPrestashop as inPrestashop
       
                
                FROM ProductView p
                 WHERE 1=1 " . $sqlFilterSeason . ' ' . $sqlFilterQuantity . ' ' . $sqlFilterStatus;


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

        /** @var CDocumentRepo $docRepo */
        $docRepo = \Monkey::app()->repoFactory->create('Document');
        $datatable->doAllTheThings();

        foreach ($datatable->getResponseSetData() as $key => $row) {
            /** @var $val CProduct */
            $val = $productRepo->findOneBy($row);

            $row["DT_RowId"] = $val->printId();
            $row["DT_RowClass"] = $val->productStatus->isVisible == 1 ? 'verde' : (
            $val->productStatus->isReady == 1 ? 'arancione' : ""
            );

            $row['code'] = $okManage ? '<a data-toggle="tooltip" title="modifica" data-placement="right" href="' . $modifica . '?id=' . $val->id . '&productVariantId=' . $val->productVariantId . '">' . $val->id . '-' . $val->productVariantId . '</a>' : $val->id . '-' . $val->productVariantId;


            $datatable->setResponseDataSetRow($key,$row);
        }
        return $datatable->responseOut();
    }
}