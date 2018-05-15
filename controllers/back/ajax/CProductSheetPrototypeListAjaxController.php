<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CContractDetails;
use bamboo\domain\entities\CContracts;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CProductDetailLabel;
use bamboo\domain\entities\CProductDetailLabelTranslation;
use bamboo\domain\entities\CProductSheetPrototype;
use bamboo\domain\repositories\CContractDetailsRepo;
use bamboo\domain\repositories\CContractsRepo;
use bamboo\domain\repositories\CFoisonRepo;
use bamboo\domain\repositories\CProductBatchRepo;
use bamboo\domain\repositories\CProductDetailLabelRepo;


/**
 * Class CProductSheetPrototypeListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 15/05/2018
 * @since 1.0
 */
class CProductSheetPrototypeListAjaxController extends AAjaxController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function get()
    {

        $sql = "
            SELECT psp.id,
              psp.name,
              group_concat(concat(pdlt.name,' Pr: ',pdl.order)) as namePr
            FROM ProductSheetPrototype psp
            JOIN ProductSheetPrototypeHasProductDetailLabel psphpdl ON psp.id = psphpdl.productSheetPrototypeId
            JOIN ProductDetailLabel pdl ON psphpdl.productDetailLabelId = pdl.id
            JOIN ProductDetailLabelTranslation pdlt ON pdl.id = pdlt.productDetailLabelId
            GROUP BY psp.id
        ";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);


        $datatable->doAllTheThings(false);


        /** @var CRepo $pspRepo */
        $pspRepo = \Monkey::app()->repoFactory->create('ProductSheetPrototype');

        /** @var CRepo $trRepo */
        $trRepo = \Monkey::app()->repoFactory->create('ProductDetailLabelTranslation');

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var CProductSheetPrototype $psp */
            $psp = $pspRepo->findOneBy(['id'=>$row['id']]);
            $row['id'] = $psp->id;
            $row['name'] = $psp->name;

            /** @var CObjectCollection $details */
            $details = $psp->productDetailLabel;

            $details->reorder('order');
            $vals = '';
            /** @var CProductDetailLabel $detail */
            foreach ($details as $detail){

                /** @var CProductDetailLabelTranslation $trName */
                $trName = $trRepo->findOneBy(['productDetailLabelId'=>$detail->id, 'langId'=>1]);

                $vals .= $trName->name . ' | Pr: ' . $detail->order . '</br>';
            }

            $row['namePr'] = $vals;

            $datatable->setResponseDataSetRow($key,$row);
        }


        return $datatable->responseOut();

    }

}