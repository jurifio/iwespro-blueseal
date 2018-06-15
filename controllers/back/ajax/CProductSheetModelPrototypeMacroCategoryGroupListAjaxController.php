<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CContractDetails;
use bamboo\domain\entities\CContracts;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CProductBatchDetails;
use bamboo\domain\entities\CProductBatchHasProductBrand;
use bamboo\domain\entities\CProductBatchHasProductName;
use bamboo\domain\entities\CProductCategory;
use bamboo\domain\entities\CProductName;
use bamboo\domain\entities\CProductSheetModelPrototypeCategoryGroup;
use bamboo\domain\entities\CProductSheetModelPrototypeMacroCategoryGroup;
use bamboo\domain\entities\CWorkCategory;
use bamboo\domain\repositories\CContractDetailsRepo;
use bamboo\domain\repositories\CContractsRepo;
use bamboo\domain\repositories\CFoisonRepo;
use bamboo\domain\repositories\CProductBatchRepo;
use bamboo\domain\repositories\CProductNameRepo;
use bamboo\domain\repositories\CWorkCategoryRepo;


/**
 * Class CProductSheetModelPrototypeMacroCategoryGroupListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 14/06/2018
 * @since 1.0
 */
class CProductSheetModelPrototypeMacroCategoryGroupListAjaxController extends AAjaxController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function get()
    {

        $sql = "
            SELECT 
            pmcg.id,
            pmcg.name,
            pmcg.description,
            pmcg.imageUrl
             FROM ProductSheetModelPrototypeMacroCategoryGroup pmcg";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);


        $datatable->doAllTheThings(false);

        /** @var CRepo $pmcgR */
        $pmcgR = \Monkey::app()->repoFactory->create('ProductSheetModelPrototypeMacroCategoryGroup');

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var CProductSheetModelPrototypeMacroCategoryGroup $pmcg */
            $pmcg = $pmcgR->findOneBy(['id'=>$row['id']]);
            $row['id'] = $pmcg->id;
            $row['name'] = $pmcg->name;
            $row['desc'] = $pmcg->description;
            $row['imageUrl'] = '<a href="#1" class="enlarge-your-img"><img width="50" src="' . $pmcg->imageUrl . '" /></a>';


            $datatable->setResponseDataSetRow($key,$row);
        }


        return $datatable->responseOut();

    }

}