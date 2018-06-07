<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CContractDetails;
use bamboo\domain\entities\CContracts;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CProductBatchDetails;
use bamboo\domain\entities\CProductBatchHasProductBrand;
use bamboo\domain\entities\CProductBatchHasProductName;
use bamboo\domain\entities\CProductBrand;
use bamboo\domain\entities\CWorkCategory;
use bamboo\domain\entities\CWorkCategorySteps;
use bamboo\domain\repositories\CContractDetailsRepo;
use bamboo\domain\repositories\CContractsRepo;
use bamboo\domain\repositories\CFoisonRepo;
use bamboo\domain\repositories\CProductBatchDetailsRepo;
use bamboo\domain\repositories\CProductBatchRepo;
use bamboo\domain\repositories\CWorkCategoryRepo;
use bamboo\domain\repositories\CWorkCategoryStepsRepo;


/**
 * Class CProductNameTranslationListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 05/06/2018
 * @since 1.0
 */
class CProductNameTranslationListAjaxController extends AAjaxController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function get()
    {
        $productBatchId = $this->data["productbatchid"];

        $sql = "
            SELECT pbhpn.productBatchId as batch,
                   pbhpn.productName as pName,
                   l.name as lang,
                   pbhpn.langId as langId
            FROM ProductBatchHasProductName pbhpn
            JOIN Lang l ON pbhpn.langId = l.id
            WHERE pbhpn.productBatchId = $productBatchId
        ";

        $datatable = new CDataTables($sql, ['pName','batch','langId'], $_GET, true);

        $datatable->doAllTheThings(false);

        /** @var CRepo $pbhpnRepo */
        $pbhpnRepo = \Monkey::app()->repoFactory->create('ProductBatchHasProductName');

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var CProductBatchHasProductName $tName */
            $tName = $pbhpnRepo->findOneBy(['productBatchId'=>$productBatchId, 'productName'=>$row['pName'], 'langId'=>$row['langId']]);

            $row['batch'] = $tName->productBatchId;
            $row['pName'] = $tName->productName;
            $row['lang'] = $tName->lang->name;

            $datatable->setResponseDataSetRow($key,$row);
        }


        return $datatable->responseOut();

    }

}