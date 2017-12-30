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
  ps1.productId,
  ps1.productVariantId,
  ps1.productSizeId as size,
  ps1.shopId as shp_id,
  concat(ps1.productId, '-', ps1.productVariantId) AS id,
  ps1.price as p_price,
  ps1.salePrice as p_sale_price,
  q1.prezzi,
  q1.prezziSaldo,
  q1.onSale as on_sale
FROM ProductSku ps1
  JOIN (SELECT
          p.id,
          p.productVariantId,
          p.isOnSale as onSale,
          count(DISTINCT ps.salePrice) AS prezziSaldo,
          count(DISTINCT ps.price) AS prezzi
        FROM Product p
          JOIN ProductSku ps ON p.id = ps.productId AND p.productVariantId = ps.productVariantId
        WHERE p.qty > 0
        GROUP BY p.id, p.productVariantId
        HAVING prezzi > 1 or prezziSaldo > 1
       ) q1 ON ps1.productId = q1.id AND ps1.productVariantId = q1.productVariantId";

        $datatable = new CDataTables($sql, ['productId','productVariantId','size','shp_id'], $_GET, true);

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