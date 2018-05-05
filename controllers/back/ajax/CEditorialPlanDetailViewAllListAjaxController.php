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
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CShooting;

class CEditorialPlanDetailViewAllListAjaxController extends AAjaxController
{

    public function get()
    {
        $sql = "SELECT n.id as id, 
        P.id as editorialPlanId,
        n.title,
        S.name as shopName,
        n.startEventDate, 
        n.endEventDate,
        n.status,
        P.name as nameEditorial, 
        P.startDate as startDateEditorial,
        P.endDate as endDateEditorial,
        E.name as socialName,
        A.titleArgument as titleArgument,
        n.description as description,
        n.photoUrl as photoUrl
        from EditorialPlanDetail n
        INNER JOIN EditorialPlan P on n.editorialPlanId =P.id
        INNER JOIN Shop S ON P.shopId = S.id
        INNER JOIN EditorialPlanSocial E ON n.socialId=E.id
        INNER JOIN EditorialPlanArgument A ON n.editorialPlanArgumentId = A.id group by id order BY startDateEditorial asc";
        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings(true);
        /** @var CEditorialPlanRepo $editorialPlanRepo */
        $editorialPlanRepo = \Monkey::app()->repoFactory->create('EditorialPlan');


        $blueseal = $this->app->baseUrl(false) . '/blueseal/';
        $opera = $blueseal . "editorial/editoriale-pianodettagli-lista/";

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var CEditorialPlan $editorialPlan */
            $editorialPlan = $editorialPlanRepo->findOneBy(['id' => $row["editorialPlanId"] ]);

            $row['id'] = '<a href="' . $opera . $editorialPlan->id . '" >' . $row['id']. '</a>';

            $row['photoUrl']='<a href="#1" class="enlarge-your-img"><img width="50" src="' . $row['photoUrl']. '" /></a>';

            switch($row['status']){
                case "Draft":
                    $row['status']="<span class=\"label label-primary\">Bozza</span>";
                    break;
                case "Approved":
                    $row['status']="<span class=\"label label-success\">Approvata</span>";
                    break;
                case "Rejected":
                    $row['status']="<span class=\"label label-danger\">Rifiutata</span>";
                    break;
                case "Pubblished":
                    $row['status']="<span class=\"label label-success\">Pubblicata</span>";
                    break;

            }


            $datatable->setResponseDataSetRow($key,$row);


        }

        return $datatable->responseOut();
    }
}