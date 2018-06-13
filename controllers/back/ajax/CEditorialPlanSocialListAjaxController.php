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

class CEditorialPlanSocialListAjaxController extends AAjaxController
{

    public function get()
    {
        $sql = "SELECT id, name, iconSocial  from EditorialPlanSocial  ";
        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings(true);
        /** @var CEditorialPlanSocialRepo $editorialPlanSocialRepo */
        $editorialPlanSocialRepo = \Monkey::app()->repoFactory->create('EditorialPlanSocial');


        $blueseal = $this->app->baseUrl(false) . '/blueseal/';
        $opera = $blueseal . "editorial/editoriale-media-lista/";

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var CEditorialPlanSocial $editorialPlanSocial */
            $editorialPlanSocial = $editorialPlanSocialRepo->findOneBy(['id' => $row["id"] ]);

            $row['id'] =  $editorialPlanSocial->id ;


            $datatable->setResponseDataSetRow($key,$row);


        }

        return $datatable->responseOut();
    }
}