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
use bamboo\domain\entities\CEditorialPlanSocial;

class CEditorialPlanArgumentListAjaxController extends AAjaxController
{

    public function get()
    {
        $sql = "SELECT e.id as id , e.titleArgument as titleArgument , e.type as type , e.descriptionArgument as descriptionArgument,
       e.editorialPlanSocialId as editorialPlanSocialId, ep.`name` as editorialPlanSocialName, w.id  as workCategoryId, w.name as workCategoryName from EditorialPlanArgument e
                left join EditorialPlanSocial ep on e.editorialPlanSocialId=ep.id 
                left join WorkCategory w on e.workCategoryId=w.id";
        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings(true);
        /** @var CEditorialPlanArgumentRepo $editorialPlanArgumentRepo */
        $editorialPlanArgumentRepo = \Monkey::app()->repoFactory->create('EditorialPlanArgument');
        $editorialPlanSocialRepo=\Monkey::app()->repoFactory->create('EditorialPlanSocial');
        $workCategoryRepo=\Monkey::app()->repoFactory->create('WorkCategory');


        $blueseal = $this->app->baseUrl(false) . '/blueseal/';
        $opera = $blueseal . "editorial/editoriale-argomento-lista/";

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var CEditorialPlanArgument $editorialPlanArgument */
            $editorialPlanArgument = $editorialPlanArgumentRepo->findOneBy(['id' => $row["id"]]);

            $row['id'] = $editorialPlanArgument->id;
            $workCategoryId = '';
            $workCategoryName='';
            if ($editorialPlanArgument->workCategoryId != null){
                $workCategoryId = $editorialPlanArgument->workCategoryId;
                $workCategory=$workCategoryRepo->findOneBy(['id'=>$workCategoryId]);
                $workCategoryName=$workCategory->name;
            }

            $row['workCategoryId']=$workCategoryId;
            $row['workCategoryName']=$workCategoryName;
            $editorialPlanSocialName='';
            if($editorialPlanArgument->editorialPlanSocialId!=null){
                $editorialPlanSocial=$editorialPlanSocialRepo->findOneBy(['id'=>$editorialPlanArgument->editorialPlanSocialId]);
                $editorialPlanSocialName=$editorialPlanSocial->name;
            }

            $row['editorialPlanSocialName']=$editorialPlanSocialName;
            $editorialPlanSocialId='';
            if($editorialPlanArgument->editorialPlanSocialId!=null) {
                $editorialPlanSocialId = $editorialPlanArgument->editorialPlanSocialId;
            }
            $row['editorialPlanSocialId']=$editorialPlanSocialId;



            $datatable->setResponseDataSetRow($key,$row);


        }

        return $datatable->responseOut();
    }
}