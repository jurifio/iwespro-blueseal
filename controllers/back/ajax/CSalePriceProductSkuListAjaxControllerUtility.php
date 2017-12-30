<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CProductSize;
use bamboo\domain\entities\CProductSizeGroup;
use bamboo\domain\entities\CProductSizeMacroGroup;
use bamboo\domain\entities\CProductSku;
use bamboo\domain\repositories\CProductSizeRepo;
use bamboo\domain\repositories\CProductSkuRepo;


/**
 * Class CProductDetailListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CSalePriceProductSkuListAjaxControllerUtility extends AAjaxController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function get()
    {
        $sql = "SELECT
  DISTINCT concat(finalPsk.productId,'-',finalPsk.productVariantId) as id,
  finalPsk.productSizeId as size,
  finalPsk.price as p_price,
  finalPsk.salePrice as p_sale_price,
  finalPsk.shopId as shp_id
  FROM (
    SELECT
      toExtrapolate.Product,
      toExtrapolate.skuID,
      toExtrapolate.skuVariant
    FROM (
        SELECT
          @a as Valore_prec,
          @b := @a as Valore_per_confronto,
          concat(p.id, '-', p.productVariantId) AS Product,
          @a := concat(p.id,'-',p.productVariantId) ignora,
          count(concat(p.id, '-', p.productVariantId)) as conto,
          psk.productId as skuID,
          psk.productVariantId as skuVariant,
          psk.price,
          psk.salePrice,
          CASE WHEN @a = @b THEN 'yes'
            ELSE NULL END AS control
        FROM Product p
         JOIN ProductSku psk on p.id = psk.productId AND p.productVariantId = psk.productVariantId
        WHERE p.isOnSale = 1 AND  p.productStatusId = 6
        GROUP BY p.id, p.productVariantId, psk.salePrice) as toExtrapolate

    WHERE control = 'yes') AS rightSku
  JOIN ProductSku finalPsk ON (finalPsk.productId, finalPsk.productVariantId) = (rightSku.skuID, rightSku.skuVariant)
  GROUP BY concat(finalPsk.productId,'-',finalPsk.productVariantId), finalPsk.productSizeId, finalPsk.shopId";

        $datatable = new CDataTables($sql, ['id','size','shp_id'], $_GET, true);

        $datatable->doAllTheThings(true);

        /** @var CProductSkuRepo $sku_repo */
       // $sku_repo = \Monkey::app()->repoFactory->create('ProductSku');

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var CProductSku $sku */
            //$sku = $sku_repo->findOneBy(['productId' => $row['productId'],
                                     // 'productVariantId' => $row['productVariantId'],
                                     // 'productSizeId' => $row['size'],
                                     // 'shopId' => $row['shp_id']
                                      //  ]);

            $datatable->setResponseDataSetRow($key,$row);
        }


        return $datatable->responseOut();
    }
}