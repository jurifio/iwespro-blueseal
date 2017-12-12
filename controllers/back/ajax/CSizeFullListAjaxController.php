<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CProductSize;
use bamboo\domain\repositories\CProductSizeRepo;



/**
 * Class CProductDetailListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CSizeFullListAjaxController extends AAjaxController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function get()
    {
        $sql = "SELECT ps.id, 
                       ps.slug, 
                       ps.name, 
                       ps.detail
                FROM ProductSize ps";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings();

        /** @var CProductSizeRepo $productSizeRepo */
        $productSizeRepo = \Monkey::app()->repoFactory->create('ProductSize');

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var CProductSize $productSize */
            $productSize = $productSizeRepo->findOne([$row['id']]);

            //$row["DT_RowId"] = $return->printId();
            $row['id'] = $productSize->id;
            $row['slug'] = $productSize->slug;
            $row['name'] = $productSize->name;
            $row['detail'] = $productSize->detail;

            $datatable->setResponseDataSetRow($key,$row);
        }


        return $datatable->responseOut();
    }
}