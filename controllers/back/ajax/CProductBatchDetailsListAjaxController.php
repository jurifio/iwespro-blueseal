<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\domain\entities\CContractDetails;
use bamboo\domain\entities\CContracts;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\repositories\CContractDetailsRepo;
use bamboo\domain\repositories\CContractsRepo;
use bamboo\domain\repositories\CFoisonRepo;
use bamboo\domain\repositories\CProductBatchDetailsRepo;
use bamboo\domain\repositories\CProductBatchRepo;


/**
 * Class CProductBatchDetailsListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 19/03/2018
 * @since 1.0
 */
class CProductBatchDetailsListAjaxController extends AAjaxController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function get()
    {
        $sql = "
            SELECT pbd.id,
                  concat(pbd.productId,'-',pbd.productVariantId),
                  pbd.workCategoryStepsId,
                  wcs.name
            FROM ProductBatchDetails pbd
            JOIN WorkCategorySteps wcs ON pbd.workCategoryStepsId = wcs.id
        ";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings(false);

        /** @var CProductBatchDetailsRepo $pbdRepo */
        $pbdRepo = \Monkey::app()->repoFactory->create('ProductBatchDetails');

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var CProductBatch $pbr */
            $pbr = $pbdRepo->findOneBy(['id'=>$row["id"]]);


            $datatable->setResponseDataSetRow($key,$row);
        }


        return $datatable->responseOut();

    }

}