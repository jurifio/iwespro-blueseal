<?php
/**
 * Created by PhpStorm.
 * User: jurif
 * Date: 06/04/2018
 * Time: 16:55
 */

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CEditorialPlan;

class CEditorialPlanListAjaxController extends AAjaxController
{

    public function get()
    {
        $sql = "SELECT n.id, n.name, S.name as shopName,  n.startDate, n.endDate from EditorialPlan n INNER JOIN Shop S ON n.shopId = S.id";
        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings(true);

        foreach ($datatable->getResponseSetData() as $key=>$row) {




        }

        return $datatable->responseOut();
    }
}