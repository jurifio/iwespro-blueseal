<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\base\CObjectCollection;
use bamboo\domain\entities\CShooting;
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
class CShootingListAjaxController extends AAjaxController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function get()
    {
        

        $sql = "SELECT
                  s.id as idShooting,
                  s.date,
                  s.friendDdt as id,
                  d1.number as ddtF,
                  s.pickyDdt,
                  d2.number as ddtP,
                  s.note,
                  sb.shopId,
                  s.pieces,
                  shp.name as shopName,
                  count(phs.shootingId) as nProduct,
                  s.printed
                FROM Shooting s
                  JOIN ProductHasShooting phs ON s.id = phs.shootingId
                  JOIN ShootingBooking sb ON s.id = sb.shootingId
                  JOIN Shop shp ON sb.shopId = shp.id
                  LEFT JOIN Document d1 ON s.friendDdt = d1.id
                  LEFT JOIN Document d2 ON s.pickyDdt = d2.id
                GROUP BY s.id
               ";

        $datatable = new CDataTables($sql, ['idShooting'], $_GET, true);

        $datatable->addCondition('shopId', \Monkey::app()->repoFactory->create('Shop')->getAutorizedShopsIdForUser());

        $datatable->doAllTheThings(false);

        $blueseal = $this->app->baseUrl(false).'/blueseal/';
        $url = $blueseal."shooting/righe-shooting/";

        /** @var CShootingRepo $sRepo */
        $sRepo = \Monkey::app()->repoFactory->create('Shooting');

        /** @var CDocumentRepo $dRepo */
        $dRepo = \Monkey::app()->repoFactory->create('Document');

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var CShooting $shooting */
            $shooting = $sRepo->findOneBy(['id'=>$row["idShooting"]]);
            $row["id"] = $shooting->friendDdt;
            $row["row_id"] = $shooting->id;
            $row["idShooting"] = '<a href="'.$url.$shooting->id.'" target="_blank">'.$shooting->id.'</a>';
            $row["date"] = $shooting->date;
            $row["ddtF"] = (is_null($shooting->friendDdt) ? "---" : $dRepo->findShootingFriendDdt($shooting));
            $row["ddtP"] = (is_null($shooting->pickyDdt) ? "---" : $dRepo->findShootingPickyDdt($shooting));
            $row["note"] = $shooting->note;
            $row["pieces"] = (is_null($shooting->pieces) ? "---" : $shooting->pieces);
            $row["nProduct"] = $shooting->product->count();
            $row["shopName"] = $shooting->shootingBooking->shop->name;
            $row["printed"] = ($shooting->printed == 0 ?  "Mai stampato" : "Già stampato" );

            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();

    }

}