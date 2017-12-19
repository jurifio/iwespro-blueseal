<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CProductSize;
use bamboo\domain\entities\CProductSizeGroup;
use bamboo\domain\entities\CProductSizeMacroGroup;
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
class CSizeMacroGroupSimpleListAjaxController extends AAjaxController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function get()
    {
        $sql = "SELECT
                  psm.id,
                  psm.name,
                  GROUP_CONCAT(psg.id) as productSizeGroups
                FROM ProductSizeMacroGroup psm
                LEFT JOIN ProductSizeGroup psg ON psm.id = psg.productSizeMacroGroupId
                GROUP BY psm.id";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings(false);

        /** @var CProductSizeMacroGroup $productSizeGroupRepo */
         $productSizeGroupRepo = \Monkey::app()->repoFactory->create('ProductSizeMacroGroup');

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var CProductSizeMacroGroup $productSizeMacroGroup */
            $productSizeMacroGroup = $productSizeGroupRepo->findOne([$row['id']]);

            $row['id'] = $productSizeMacroGroup->id;
            $row['name'] = $productSizeMacroGroup->name;

            /** @var CProductSizeGroup $allGroupSize */
            $allGroupSize = $productSizeMacroGroup->productSizeGroup->findByKey('productSizeMacroGroupId',$row['id']);

            if (!empty($allGroupSize)){
                foreach ($allGroupSize as $singleProductSizeGroup) {
                    $row['productSizeGroups'][] = " ".$singleProductSizeGroup->name;
                }
            } else {
                $row['productSizeGroups'] = "---";
            }



            $datatable->setResponseDataSetRow($key,$row);
        }


        return $datatable->responseOut();
    }
}