<?php
/**
 *
 */

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CWishList;
use bamboo\domain\entities\CUser;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CCart;
use bamboo\domain\entities\CProductPublicSku;
use bamboo\domain\entities\CCartLine;

use bamboo\utils\time\STimeToolbox;
use bamboo\utils\price\SPriceToolbox;

class CCartAbandonedListAjaxController extends AAjaxController
{

    public function get()
    {
        $sql = "SELECT
  C.id                                                   AS id,
  C.creationDate                                         AS creationDate,
  concat(ud.name, ' ', ud.surname)                       AS user,
  U.email                                                AS email,
  IF(p.isOnSale = 1, sum(pps.salePrice), sum(pps.Price)) AS price,
  C.cartTypeId                                           AS carTypeId,
  C.lastUpdate                                           AS lastUpdate
FROM Cart C
  INNER JOIN User U ON C.userId = U.id
  INNER JOIN UserDetails ud ON C.id = ud.userId
  INNER JOIN CartLine Cl ON C.id = Cl.cartId
  INNER JOIN ProductPublicSku pps ON Cl.productId = pps.productId AND Cl.productVariantId = pps.productVariantId
  INNER JOIN Product p ON Cl.productId = p.id AND Cl.productVariantId = p.productVariantId

WHERE C.userId <> ''
      AND C.cartTypeId = 3
GROUP BY C.id";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings(true);

        foreach ($datatable->getResponseSetData() as $key => $row) {


            $row['creationDate'] = STimeToolbox::FormatDateFromDBValue($row['creationDate'], 'd-m-Y H:i:s');
            $row['lastUpdate'] = STimeToolbox::FormatDateFromDBValue($row['lastUpdate'], 'd-m-Y H:i:s');

            $row['price'] = "<span class=\"label label-warning\">" . SPriceToolbox::formatToEur($row['price'], true) . "</span>";


            $datatable->setResponseDataSetRow($key, $row);


        }

        return $datatable->responseOut();

    }
}