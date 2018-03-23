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
class CShootingDetailsListAjaxController extends AAjaxController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function get()
    {

        $shootingId = $this->data["shootingid"];

        $sql = "SELECT concat(phs.productId,'-',phs.productVariantId) as DT_RowId,
                  phs.productId,
                  phs.productVariantId,
                  phs.shootingId,
                  phs.creationDate,
                  sp.name as shopName
                FROM ProductHasShooting phs
                  JOIN Shooting s ON phs.shootingId = s.id
                  JOIN Product p ON phs.productVariantId = p.productVariantId
                  JOIN Shop sp ON s.shopId = sp.id
                WHERE s.id = $shootingId
                GROUP BY phs.productId, phs.productVariantId
               ";

        $datatable = new CDataTables($sql, ['productId','productVariantId'], $_GET, true);

        $datatable->doAllTheThings(true);


        foreach ($datatable->getResponseSetData() as $key=>$row) {

            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();

    }

}