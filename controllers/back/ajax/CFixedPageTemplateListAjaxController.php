<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\db\pandaorm\repositories\CRepo;

/**
 * Class CFixedPageTemplateListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 23/04/2019
 * @since 1.0
 */

class CFixedPageTemplateListAjaxController extends AAjaxController
{

    public function get()
    {
        $sql = "
                SELECT fpt.id,
                       fpt.name
                FROM FixedPageTemplate fpt 
                ";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings();

        $url = $this->app->baseUrl(false) . '/blueseal/manage-fixed-page/fixed-page-template/';

        /** @var CRepo $fixedPageTemplateRepo */
        $fixedPageTemplateRepo = \Monkey::app()->repoFactory->create('FixedPageTemplate');

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var $fixedPageTemplate $fixedPageTemplate */
            $fixedPageTemplate = $fixedPageTemplateRepo->findOneBy(['id'=>$row['id']]);

            $row['DT_RowId'] = $fixedPageTemplate->id;
            $row['id'] = "<a href='{$url}{$fixedPageTemplate->id}' target='_blank'>$fixedPageTemplate->id</a>";
            $row['name'] = $fixedPageTemplate->name;

            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();
    }
}