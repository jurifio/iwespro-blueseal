<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;

use bamboo\domain\entities\CNewsletterEvent;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductHasShopDestination;
use bamboo\domain\entities\CShop;
use bamboo\domain\entities\CShooting;
use bamboo\domain\repositories\CDocumentRepo;

/**
 * Class CProductHasShopDestinationListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 24/05/2019
 * @since 1.0
 */
class CProductHasShopDestinationListAjaxController extends AAjaxController
{
    public function get()
    {


        $sql = "SELECT  
            concat(p.id, '-', pv.id)                                                                      AS id,
         p.id AS productId,
         p.productVariantId AS productVariantId,
        (select group_concat(distinct concat(siou.id,'-',siou.name)) from Shop siou join ShopHasProduct phsd3 on siou.id=phsd3.shopId where phsd3.productId=p.id 
                                                                                                                                          and phsd3.productVariantId=p.productVariantId)   AS shopIdOrigin,
    (select group_concat(distinct concat(sidu.id,'-',sidu.name)) from Shop sidu join ProductHasShopDestination phsd1 on sidu.id=phsd1.shopIdDestination where phsd1.productId=p.id 
                                                                                                                                          and phsd1.productVariantId=p.productVariantId) as  shopNameDestination, 
     (SELECT ifnull(group_concat(distinct ma.name), '')
                   FROM Marketplace m
                     JOIN MarketplaceAccount ma ON m.id = ma.marketplaceId
                     JOIN MarketplaceAccountHasProduct mahp ON (ma.id,ma.marketplaceId) = (mahp.marketplaceAccountId,mahp.marketplaceId)
                   WHERE mahp.productId = p.id AND
                         mahp.productVariantId = p.productVariantId AND mahp.isDeleted != 1)                            AS marketplace,
 (select group_concat(distinct concat(su.id,'-',su.name,'-',stu.name)) from ProductStatus stu join ProductHasShopDestination phsd2 on stu.id=phsd2.statusId 
     join Shop su on phsd2.shopIdDestination=su.id
  where phsd2.productId=p.id and phsd2.productVariantId=p.productVariantId) as  ProductShopStatusDestination, 
 
         pst.name  AS status,
         p.qty AS qty,
         ps.name AS season,
        pb.name as brand,
        if(p.isOnSale=1,'Si','No') AS isOnSale,
          concat(p.itemno, ' # ', pv.name)                                                              AS cpf,
         p.dummyPicture AS dummyPicture,
          (SELECT group_concat(DISTINCT t.name)
                  FROM ProductHasTag pht
                    JOIN TagTranslation t ON pht.tagId = t.tagId
                  WHERE langId = 1 AND pht.productId = p.id AND pht.productVariantId = p.productVariantId)   AS tags

FROM Product p  
                  LEFT  JOIN ProductHasShopDestination phsd ON p.id=phsd.productId AND p.productVariantId=phsd.productVariantId 
JOIN ProductSeason ps ON p.productSeasonId=ps.id
join ShopHasProduct SHP on p.id = SHP.productId and p.productVariantId = SHP.productVariantId
left JOIN Shop sd ON phsd.shopIdDestination=sd.id
left JOIN Shop so ON phsd.shopIdOrigin=so.id
JOIN ProductStatus pst ON p.productStatusId=pst.id
JOIN  ProductBrand pb ON p.productBrandId=pb.id
left JOIN ProductStatus pstd ON phsd.statusId=pstd.id
JOIN ProductVariant pv ON p.productVariantId = pv.id
WHERE p.qty>0  and p.productStatusId=6";
        $productRepo = \Monkey ::app() -> repoFactory -> create('Product');

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable -> doAllTheThings(true);
        foreach ($datatable -> getResponseSetData() as $key => $row) {
            /** @var $val CProduct */
            $val = $productRepo -> findOneBy($row);
            $row["DT_RowId"] = $val -> printId();
            $row['dummyPicture'] = '<a href="#1" class="enlarge-your-img"><img width="50" src="' . $val -> getDummyPictureUrl() . '" /></a>';
            $row['tags'] = '<span class="small">' . $val -> getLocalizedTags('<br>', false) . '</span>';
            $row['marketplace'] = $val->getMarketplaceAccountsNameShopDestination(' - ','<br>',true);
           // $row['shopNameDestination'] = $val->getShopIdOriginName(' - ','<br>',true);

            $datatable -> setResponseDataSetRow($key, $row);
        }


        return $datatable -> responseOut();
    }
}