<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\domain\entities\CShooting;
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
        $sql = "SELECT s.id,
                        s.date,
                        s.friendDdt,
                        s.pickyDdt,
                        s.note,
                        s.phase,
                        s.shopId,
                        shp.name as shopName
               FROM Shooting s
               JOIN Shop shp ON s.shopId = shp.id
               ";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings(false);

        $blueseal = $this->app->baseUrl(false).'/blueseal/';
        $url = $blueseal."shooting/righe-shooting/";

        /** @var CShootingRepo $sRepo */
        $sRepo = \Monkey::app()->repoFactory->create('Shooting');

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var CShooting $shooting */
            $shooting = $sRepo->findOneBy(['id'=>$row["id"]]);
            $row["row_id"] = $shooting->id;
            $row["id"] = '<a href="'.$url.$shooting->id.'" target="_blank">'.$shooting->id.'</a>';
            $row["date"] = $shooting->date;
            $row["friendDdt"] = $shooting->friendDdt;
            $row["pickyDdt"] = $shooting->pickyDdt;
            $row["note"] = $shooting->note;
            $row["phase"] = $shooting->phase;
            $row["shopName"] = $shooting->shop->name;

            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();

    }

}