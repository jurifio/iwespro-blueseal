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
use bamboo\domain\entities\CWorkCategory;
use bamboo\domain\repositories\CContractDetailsRepo;
use bamboo\domain\repositories\CContractsRepo;
use bamboo\domain\repositories\CFoisonRepo;
use bamboo\domain\repositories\CProductBatchRepo;
use bamboo\domain\repositories\CProductNameRepo;
use bamboo\domain\repositories\CWorkCategoryRepo;


/**
 * Class CProductSheetModelPrototypeCategoryGroupListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 12/06/2018
 * @since 1.0
 */
class CProductSheetModelPrototypeCategoryGroupListAjaxController extends AAjaxController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function get()
    {

        $sql = "
            SELECT
              catG.id,
              catG.name,
              catG.description,
              if((count(pmp.id) = 0), 'v', count(pmp.id)) as models,
              if((isnull(catG.imageUrl)), 'no', 'sì') as image,
              catMacroG.id as macroId,
              catMacroG.name as macroName
            FROM ProductSheetModelPrototypeCategoryGroup catG
            LEFT JOIN ProductSheetModelPrototypeMacroCategoryGroup catMacroG ON catG.macroCategoryGroupId = catMacroG.id
            LEFT JOIN ProductSheetModelPrototype pmp ON catG.id = pmp.categoryGroupId
            GROUP BY catG.id
        ";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);


        $datatable->doAllTheThings(false);

        /** @var CRepo $pSheetCataGroupRepo */
        $pSheetCataGroupRepo = \Monkey::app()->repoFactory->create('ProductSheetModelPrototypeCategoryGroup');

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var CProductSheetModelPrototypeCategoryGroup $cat */
            $cat = $pSheetCataGroupRepo->findOneBy(['id'=>$row["id"]]);

            $row['id'] = $cat->id;
            $row['name'] = $cat->name;
            $row['description'] = $cat->description;
            $row['models'] = $cat->productSheetModelPrototype->count();
            $row['image'] = '<a href="#1" class="enlarge-your-img"><img width="50" src="' . $cat->imageUrl . '" /></a>';
            $row['macroName'] = (is_null($cat->macroCategoryGroupId) ? '-' : $cat->productSheetModelPrototypeMacroCategoryGroup->name);

            $datatable->setResponseDataSetRow($key,$row);
        }


        return $datatable->responseOut();

    }

}