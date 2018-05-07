<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\base\CObjectCollection;
use bamboo\domain\entities\CShooting;
use bamboo\domain\entities\CShootingBooking;
use bamboo\domain\entities\CShootingBookingHasProductType;
use bamboo\domain\entities\CUser;
use bamboo\domain\repositories\CDocumentRepo;
use bamboo\domain\repositories\CShootingRepo;


/**
 * Class CSizeMacroGroupListAjaxController
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
class CShootingBookingListAjaxController extends AAjaxController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function get()
    {
        

        $sql = "SELECT
                  sb.id,
                  sb.date as creationDate,
                  sb.bookingDate,
                  sb.shopId,
                  s.name as shopName,
                  sb.shootingId,
                  uniqueQty as uniqueQty,
                  sb.status
                FROM ShootingBooking sb
                  JOIN Shop s ON sb.shopId = s.id
                  LEFT JOIN (
                      SELECT group_concat(concat(spt.name, ' = ', sbhpt.qty)) uniqueQty, sbhpt.shootingBookingId
                        FROM ShootingBookingHasProductType sbhpt
                        JOIN ShootingProductType spt ON sbhpt.shootingProductTypeId = spt.id
                    GROUP BY sbhpt.shootingBookingId
                    ) t ON t.shootingBookingId = sb.id
               ";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->addCondition('shopId', \Monkey::app()->repoFactory->create('Shop')->getAutorizedShopsIdForUser());

        $datatable->doAllTheThings(false);


        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var CShootingBooking $sb */
            $sb = \Monkey::app()->repoFactory->create('ShootingBooking')->findOneBy(['id'=>$row['id']]);

            $row["id"] = $sb->id;
            $row["creationDate"] = $sb->date;
            $row["bookingDate"] = $sb->bookingDate;
            $row["shopName"] = $sb->shop->name;
            $row["shootingId"] = $sb->shootingId;
            $row["c_bookingId"] = $sb->id;

            if($sb->status == "o"){
                $st = "aperto";
            } else if($sb->status == "a") {
                $st = "accettato";
            } else if($sb->status == "c"){
                $st = "chiuso";
            }

            $row["status"] = $st;

            /** @var CObjectCollection $cats */
            $cats = $sb->shootingBookingHasProductType;

            $all = "";
            /** @var CShootingBookingHasProductType $cat */
            foreach ($cats as $cat){
                $all .= $cat->shootingProductType->name.' = '.$cat->qty.'<br />';
            }

            $row["uniqueQty"] = $all;

            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();

    }

}