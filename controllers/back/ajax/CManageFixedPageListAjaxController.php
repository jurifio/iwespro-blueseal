<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\domain\entities\CFixedPage;
use bamboo\domain\repositories\CFixedPageRepo;


/**
 * Class CManageFixedPageListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 19/02/2019
 * @since 1.0
 */

class CManageFixedPageListAjaxController extends AAjaxController
{

    /**
     * @return string
     */
    public function get()
    {
        $sql = "SELECT id,
                       langId,
                       fixedPageTypeId,
                       title
                FROM FixedPage
                WHERE fixedPageTypeId = 1";

        $datatable = new CDataTables($sql, ['id', 'langId', 'fixedPageTypeId'], $_GET, true);

        /** @var CFixedPageRepo $fixedPageRepo */
        $fixedPageRepo = \Monkey::app()->repoFactory->create('FixedPage');

        $url = $this->app->baseUrl(false).'/blueseal/manage-fixed-page/';
        $datatable->doAllTheThings(false);

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var CFixedPage $fP */
            $fP = $fixedPageRepo->findOneBy(["id"=>$row["id"], 'langId'=>$row['langId'], 'fixedPageTypeId'=>$row['fixedPageTypeId']]);


            $row["id"] = '<a href="'. $url . $fP->id . '/' . $fP->langId . '">' . $fP->id . ' - ' . $fP->lang->name . '</a>';
            $row['langId'] = $fP->lang->name;
            $row['title'] = $fP->title;

            $datatable->setResponseDataSetRow($key,$row);
        }


        return $datatable->responseOut();
    }


}