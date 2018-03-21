<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\domain\entities\CProduct;
use bamboo\domain\repositories\CProductRepo;

/**
 * Class CProductWorkListAjaxController
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
class CProductWorkListAjaxController extends AAjaxController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function get()
    {
        $sql = "
            SELECT concat(p.id, '-', p.productVariantId) as productCode, 
                   p.id,
                   p.productVariantId,
                   ps.name as productStatus,
                   p.dummyPicture,
                   pb.name as productBrand,
                   pse.name as productSeason
            FROM Product p
            JOIN ProductStatus ps ON p.productStatusId = ps.id
            JOIN ProductBrand pb ON p.productBrandId = pb.id
            JOIN ProductSeason pse ON p.productSeasonId = pse.id
            WHERE p.processing <> 'definito'
        ";

        $datatable = new CDataTables($sql, ['id', 'productVariantId'], $_GET, true);

        $datatable->doAllTheThings(false);

        /** @var CProductRepo $productRepo */
        $productRepo = \Monkey::app()->repoFactory->create('Product');

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var CProduct $product */
            $product = $productRepo->findOneBy($row);

            $row['productCode'] = $product->printId();
            $row['productStatus'] = $product->productStatus->name;
            $row['dummyPicture'] = '<img width="50" src="' . $product->getDummyPictureUrl() . '" />';
            $row['productBrand'] = $product->productBrand->name;
            $row['productSeason'] = $product->productSeason->name;

            $datatable->setResponseDataSetRow($key,$row);
        }


        return $datatable->responseOut();

    }

}