<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\domain\entities\CProductHasShooting;
use bamboo\domain\entities\CShooting;
use bamboo\domain\repositories\CProductHasShootingRepo;
use bamboo\domain\repositories\CShootingRepo;


/**
 * Class CShootingDetailsListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 23/03/2018
 * @since 1.0
 */
class CShopConfigurationListAjaxController extends AAjaxController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function get()
    {



        $sql = "SELECT 
                sr.id as id,
                sr.shopId as shopId,
                s.`name` as shopName,
                if(sr.freeShipping=1,'si','no') as freeShipping,
                 if(sr.bannerHeadActive=1,'si','no') as bannerHeadActive,
                 if(sr.bannerProductActive=1,'si','no') as bannerProductActive,
                if(sr.backgroundColor is null,'nessuno',sr.backgroundColor) as backgroundColor,
                if(sr.isBold=1,'si','no') as isBold
                from ShopRules sr join Shop s on sr.shopId=s.id 
               ";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings(false);

        /** @var CShopRules $shopRules */
        $shopRules = \Monkey::app()->repoFactory->create('Shooting')->findOneBy(['id'=>$shootingId]);

        /** @var CProductHasShootingRepo $phsRepo */
        $phsRepo = \Monkey::app()->repoFactory->create('ProductHasShooting');

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var CProductHasShooting $phs */
            $phs = $phsRepo->findOneBy(['productId'=>$row["productId"], 'productVariantId'=>$row["productVariantId"], 'shootingId'=>$shootingId, 'progressiveLineNumber'=>$row["progressiveLineNumber"]]);
            $row["productId"] = $phs->productId;
            $row["productVariantId"] = $phs->productVariantId;
            $row["progressiveLineNumber"] = $phs->progressiveLineNumber;
            $row["creationDate"] = $phs->creationDate;
            $row["shootingId"] = $phs->shootingId;
            $row["shopName"] = $shooting->shootingBooking->shop->name;
            $row["DT_RowId"] = $phs->productId.'-'.$phs->productVariantId;
            $row["season"] = $phs->product->productSeason->name.'-'.$phs->product->productSeason->year;
            $row["externalId"] = $phs->product->getShopExtenalIds(', ');
            $row["cpf"] = $phs->product->printCpf();
            $row['dummy'] = '<a href="#1" class="enlarge-your-img"><img width="50" src="' . $phs->product->getDummyPictureUrl() . '" /></a>';
            $row["productBrandName"] = $phs->product->productBrand->name;
            $row["lastAztecPrint"] = ($phs->lastAztecPrint == 0 ? "-" : $phs->lastAztecPrint);

            $qty = 0;
            foreach ($phs->product->productSku as $sku) {
                $qty += $sku->stockQty;
            }
            $row["hasQty"] = $qty;

            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();

    }

}