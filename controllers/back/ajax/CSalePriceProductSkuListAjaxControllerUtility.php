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
  ps.name as size,
  concat(ps1.productId, '-', ps1.productVariantId) AS id,
  ps1.price as p_price,
  ps1.salePrice as p_sale_price,
  q1.onSale as on_sale
FROM ProductPublicSku ps1
  JOIN ProductSize ps on ps1.productSizeId = ps.id
  JOIN (SELECT
          p.id,
          p.productVariantId,
          p.isOnSale as onSale,
          count(DISTINCT pps.salePrice) AS prezziSaldo,
          count(DISTINCT pps.price) AS prezzi
        FROM Product p
          JOIN ProductPublicSku pps ON p.id = pps.productId AND p.productVariantId = pps.productVariantId
        WHERE p.qty > 0 AND pps.salePrice = 0 AND p.isOnSale = 1
        GROUP BY p.id, p.productVariantId
        HAVING prezzi = 1
       ) q1 ON ps1.productId = q1.id AND ps1.productVariantId = q1.productVariantId";

        $datatable = new CDataTables($sql, ['productId','productVariantId','size'], $_GET, true);

        $datatable->doAllTheThings(true);

        return $datatable->responseOut();
    }
}