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
        $sql = "SELECT id, titleArgument, type, descriptionArgument from EditorialPlanArgument  ";
        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings(true);
        /** @var CEditorialPlanArgumentRepo $editorialPlanArgumentRepo */
        $editorialPlanArgumentRepo = \Monkey::app()->repoFactory->create('EditorialPlanArgument');


        $blueseal = $this->app->baseUrl(false) . '/blueseal/';
        $opera = $blueseal . "editorial/editoriale-argomento-lista/";

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var CEditorialPlanArgument $editorialPlanArgument */
            $editorialPlanArgument = $editorialPlanArgumentRepo->findOneBy(['id' => $row["id"] ]);

            $row['id'] =  $editorialPlanArgument->id ;


            $datatable->setResponseDataSetRow($key,$row);


        }

        return $datatable->responseOut();
    }
}