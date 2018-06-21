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
        /** @var CEditorialPlanRepo $editorialPlanRepo */
        $editorialPlanRepo = \Monkey::app()->repoFactory->create('EditorialPlan');


        $blueseal = $this->app->baseUrl(false) . '/blueseal/';
        $opera = $blueseal . "editorial/editoriale-pianodettagli-lista/";

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var CEditorialPlan $editorialPlan */
            $editorialPlan = $editorialPlanRepo->findOneBy(['id' => $row["id"] ]);

            $row['id'] = '<a href="' . $opera . $editorialPlan->id . '">' . $editorialPlan->id . '</a>';


            $datatable->setResponseDataSetRow($key,$row);


        }

        return $datatable->responseOut();
    }
    public function put(){
        $data  = $this->app->router->request()->getRequestData();
        $id = $data["id"];
        if (strlen($id)>10) {
            $finalpositionId = strpos($id, '</a>');
            $initialpositionId = strpos($id, '">');
            $finalpositionId = $finalpositionId;
            $initialpositionId = $initialpositionId + 2;
            $lenghtposition = $finalpositionId - $initialpositionId;
            $id = substr($id, $initialpositionId, $lenghtposition);
        }
        /** @var CRepo $editorialPlan */
        $editorialPlan = \Monkey::app()->repoFactory->create('editorialPlan');

        /** @var CEditorialPlan $editorial */
        $editorial= $editorialPlan->findOneBy(['id'=>$id]);
        $editorial->delete();
        $res = "Piano Editoriale Cancellato";
        return $res;

    }
}