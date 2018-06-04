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
use bamboo\domain\entities\CProductBrand;
use bamboo\domain\entities\CWorkCategory;
use bamboo\domain\repositories\CContractDetailsRepo;
use bamboo\domain\repositories\CContractsRepo;
use bamboo\domain\repositories\CFoisonRepo;
use bamboo\domain\repositories\CProductBatchDetailsRepo;
use bamboo\domain\repositories\CProductBatchRepo;
use bamboo\domain\repositories\CWorkCategoryRepo;


/**
 * Class CEmptyProductBrandBatchDetailsListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 04/06/2018
 * @since 1.0
 */
class CEmptyProductBrandBatchDetailsListAjaxController extends AAjaxController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function get()
    {
        $productBatchId = $this->data["productbatchid"];

        $sql = "
            SELECT 
              pBatch.id,
              pBrand.id as pBrandId,
              pBrand.slug,
              pBrand.name,
              pBrand.description,
              pBrand.logoUrl
            FROM ProductBatch pBatch
            JOIN ProductBatchHasProductBrand bb ON pBatch.id = bb.productBatchId
            JOIN ProductBrand pBrand ON bb.productBrandId = pBrand.id
            WHERE pBatch.id = $productBatchId
        ";

        $datatable = new CDataTables($sql, ['id','pBrandId'], $_GET, true);

        $datatable->doAllTheThings(false);

        /** @var CProductBatchRepo $pBatchRepo */
        $pBatchRepo = \Monkey::app()->repoFactory->create('ProductBatch');


        /** @var CProductBatch $productBatch */
        $productBatch = $pBatchRepo->findOneBy(['id'=>$productBatchId]);

        /** @var CObjectCollection $pBrands */
        $pBrands = $productBatch->productBrand;

        foreach ($datatable->getResponseSetData() as $key=>$row) {


            /** @var CProductBrand $brand */
            $brand = $pBrands->findOneByKey('id', $row['pBrandId']);

            $row['id'] = $productBatch->id;
            $row['DT_RowId'] = $brand->id;
            $row['pBrandId'] = $brand->id;
            $row['slug'] = $brand->slug;
            $row['name'] = $brand->name;
            $row['description'] = $brand->description;
            $row['logoUrl'] = $brand->logoUrl;

            $datatable->setResponseDataSetRow($key,$row);
        }


        return $datatable->responseOut();

    }

}