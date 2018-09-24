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
class CSalePriceProductPublicSkuAllListAjaxControllerUtility extends AAjaxController
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
  ps.name as size,
  concat(ps1.productId, '-', ps1.productVariantId) AS id,
  ps1.price as p_price,
  ps1.salePrice as p_sale_price,
  p.isOnSale as on_sale,
  s.name as shopName,
  dPr.dirPrice
FROM ProductPublicSku ps1
  JOIN ProductSize ps on ps1.productSizeId = ps.id
  JOIN Product p ON ps1.productId = p.id AND ps1.productVariantId = p.productVariantId
  JOIN ShopHasProduct shp ON p.id = shp.productId AND p.productVariantId = shp.productVariantId
  JOIN Shop s ON shp.shopId = s.id
  LEFT JOIN (
    SELECT dp.productId dirtyProductId, dp.productVariantId dirtyVariantId, ds.productSizeId dirtySizeId, ds.price dirPrice
    FROM DirtyProduct dp
    JOIN DirtySku ds ON ds.dirtyProductId = dp.id
  ) dPr ON dPr.dirtyProductId = ps1.productId AND dPr.dirtyVariantId = ps1.productVariantId AND dPr.dirtySizeId = ps1.productSizeId";

        $datatable = new CDataTables($sql, ['productId','productVariantId','size'], $_GET, true);

        $datatable->doAllTheThings(true);

        return $datatable->responseOut();
    }
}