<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\base\CObjectCollection;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductBatchDetails;
use bamboo\domain\entities\CProductDescriptionTranslation;
use bamboo\domain\repositories\CProductBatchDetailsRepo;
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
            SELECT 
              p.id,
              p.productVariantId,
              ps.name AS productStatus,
              p.dummyPicture,
              pb.name AS productBrand,
              pse.name AS productSeason,
                   
            
            
                   pc.id                                                                                             AS categoryId,
                   pv.description                                                                                    AS colorNameManufacturer,
                   pcg.name                                                                                          AS colorGroup,
                   concat(s.id,'-',s.name) AS Shop,
                   if(((SELECT count(0)
                        FROM ProductSheetActual
                        WHERE ((ProductSheetActual.productId = p.id) AND
                               (ProductSheetActual.productVariantId = p.productVariantId))) > 2), 'sì', 'no')    AS hasDetails
            FROM Product p
              
              JOIN ProductStatus ps ON p.productStatusId = ps.id
              JOIN ProductBrand pb ON p.productBrandId = pb.id
              JOIN ProductSeason pse ON p.productSeasonId = pse.id
              LEFT JOIN (ProductHasProductCategory ppc
                JOIN ProductCategory pc ON ppc.productCategoryId = pc.id
                ) ON (p.id, p.productVariantId) = (ppc.productId,ppc.productVariantId)
              JOIN ProductVariant pv ON p.productVariantId = pv.id
              LEFT JOIN ProductColorGroup pcg ON p.productColorGroupId = pcg.id
              JOIN ShopHasProduct sp
                ON (p.id, p.productVariantId) = (sp.productId, sp.productVariantId)
              JOIN Shop s ON s.id = sp.shopId
            WHERE p.processing <> 'definito' AND p.productStatusId IN (6,11)";

        $datatable = new CDataTables($sql, ['id', 'productVariantId'], $_GET, true);

        $datatable->doAllTheThings(false);

        /** @var CProductBatchDetailsRepo $pbdRepo */
        $pbdRepo = \Monkey::app()->repoFactory->create('ProductBatchDetails');

        /** @var CProductRepo $productRepo */
        $productRepo = \Monkey::app()->repoFactory->create('Product');

        foreach ($datatable->getResponseSetData() as $key => $row) {

            /** @var CProduct $product */
            $product = $productRepo->findOneBy($row);

            $row["DT_RowId"] = $product->printId();
            $row['productId'] = $product->id;
            $row['productVariantId'] = $product->productVariantId;
            $row['productStatus'] = $product->productStatus->name;
            $row['dummyPicture'] = '<img width="50" src="' . $product->getDummyPictureUrl() . '" />';
            $row['productBrand'] = $product->productBrand->name;
            $row['productSeason'] = $product->productSeason->name;
            $row['productCard'] = (!$product->getProductCardUrl() ? '-' : '<a href="#1" class="enlarge-your-img"><img width="50" src="' . $product->getProductCardUrl() . '" /></a>');

            /** @var CObjectCollection $pbds */
            $pbds = $pbdRepo->findBy(['productId' => $product->id, 'productVariantId' => $product->productVariantId]);

            $pbdsIds = "";
            if (!$pbds->isEmpty()) {
                /** @var CProductBatchDetails $pbd */
                foreach ($pbds as $pbd) {
                    $pbdsIds .= $pbd->productBatchId . ', ';
                }
            } else {
                $pbdsIds = 0;
            }


            $row['productBatchNumber'] = $pbdsIds;
            $row['categoryId'] = '<span class="small">' . $product->getLocalizedProductCategories(" ", "<br>") . '</span>';
            $row['productName'] = $product->productNameTranslation->getFirst() ? $product->productNameTranslation->getFirst()->name : "";
            $row['colorNameManufacturer'] = $product->productVariant->description;
            $row['colorGroup'] = '<span class="small">' . (!is_null($product->productColorGroup) ? $product->productColorGroup->productColorGroupTranslation->getFirst()->name : "[Non assegnato]") . '</span>';
            $row['shop'] = '<span class="small">' . $product->getShops('<br />', true) . '</span>';
            $row['hasDetails'] = (2 < $product->productSheetActual->count()) ? 'sì' : 'no';
            $row['details'] = "";
            foreach ($product->productSheetActual as $k => $v) {
                if ($trans = $v->productDetail->productDetailTranslation->getFirst()) {
                    $row['details'] .= '<span class="small">' . $trans->name . "</span><br />";
                }
            }
            /** @var CProductDescriptionTranslation $descT */
            $descT = $product->productDescriptionTranslation->findOneByKeys(['marketplaceId' => 1, 'langId' => 1]);
            $row['pDescTranslate'] = $descT ? $descT->description : '';

            $datatable->setResponseDataSetRow($key, $row);
        }


        return $datatable->responseOut();

    }

}