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
            SELECT concat(p.id, '-', p.productVariantId) as DT_RowId,
              p.id,
              p.productVariantId,
                   ps.name as productStatus,
              p.dummyPicture,
                   pb.name as productBrand,
                   pse.name as productSeason,
                   if((p.id, p.productVariantId) IN (SELECT
                                                       ProductCardPhoto.productId,
                                                       ProductCardPhoto.productVariantId
                                                     FROM ProductCardPhoto), 'sì', 'no')                 AS productCard,
            
                   if((pbd.productBatchId) IN (SELECT pbd.productBatchId
                                               FROM ProductBatch pb1), group_concat(pbd.productBatchId), 0)                 AS productBatchNumber,
            
              #group_concat(pbd.productBatchId) as productBatchNumber,
                   pc.id                                                                                             AS categoryId,
                   pv.description                                                                                    AS colorNameManufacturer,
                   pcg.name                                                                                          AS colorGroup,
                   concat(s.id,'-',s.name) as Shop,
                   if(((SELECT count(0)
                        FROM ProductSheetActual
                        WHERE ((ProductSheetActual.productId = p.id) AND
                               (ProductSheetActual.productVariantId = p.productVariantId))) > 2), 'sì', 'no')    AS hasDetails,
              pdtA.description as pDescTranslate
            FROM Product p
              LEFT JOIN ProductCardPhoto pcp ON p.id = pcp.productId AND p.productVariantId = p.productVariantId
              JOIN ProductStatus ps ON p.productStatusId = ps.id
              JOIN ProductBrand pb ON p.productBrandId = pb.id
              JOIN ProductSeason pse ON p.productSeasonId = pse.id
              LEFT JOIN ProductBatchDetails pbd ON p.id = pbd.productId AND p.productVariantId = pbd.productVariantId
              LEFT JOIN (ProductHasProductCategory ppc
                JOIN ProductCategory pc ON ppc.productCategoryId = pc.id
                ) ON (p.id, p.productVariantId) = (ppc.productId,ppc.productVariantId)
              JOIN ProductVariant pv ON p.productVariantId = pv.id
              LEFT JOIN ProductColorGroup pcg ON p.productColorGroupId = pcg.id
              JOIN ShopHasProduct sp
                ON (p.id, p.productVariantId) = (sp.productId, sp.productVariantId)
              JOIN Shop s ON s.id = sp.shopId
              LEFT JOIN
              (SELECT p1.id, p1.productVariantId, pdt1.description
               FROM Product p1
                 JOIN ProductDescriptionTranslation pdt1 ON p1.id = pdt1.productId AND p1.productVariantId = pdt1.productVariantId
               WHERE pdt1.marketplaceId = 1 AND pdt1.langId = 1) as pdtA ON pdtA.id = p.id AND pdtA.productVariantId = p.productVariantId
            WHERE p.processing <> 'definito'
            GROUP BY p.id, p.productVariantId";

        $datatable = new CDataTables($sql, ['id', 'productVariantId'], $_GET, true);

        $datatable->doAllTheThings(false);

        /** @var CProductBatchDetailsRepo $pbdRepo */
        $pbdRepo = \Monkey::app()->repoFactory->create('ProductBatchDetails');

        /** @var CProductRepo $productRepo */
        $productRepo = \Monkey::app()->repoFactory->create('Product');

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var CProduct $product */
            $product = $productRepo->findOneBy($row);

            $row["DT_RowId"] = $product->printId();
            $row['productStatus'] = $product->productStatus->name;
            $row['dummyPicture'] = '<img width="50" src="' . $product->getDummyPictureUrl() . '" />';
            $row['productBrand'] = $product->productBrand->name;
            $row['productSeason'] = $product->productSeason->name;
            $row['productCard'] = (!$product->getProductCardUrl() ? '-' :'<a href="#1" class="enlarge-your-img"><img width="50" src="' . $product->getProductCardUrl() . '" /></a>');

            /** @var CObjectCollection $pbds */
            $pbds = $pbdRepo->findBy(['productId'=>$product->id, 'productVariantId'=>$product->productVariantId]);

            $pbdsIds = "";
            if(!$pbds->isEmpty()){
                /** @var CProductBatchDetails $pbd */
                foreach ($pbds as $pbd){
                    $pbdsIds .= $pbd->productBatchId.', ';
                }
            } else {
                $pbdsIds = 0;
            }


            $row['productBatchNumber'] = $pbdsIds;
            $row['categoryId'] = '<span class="small">' . $product->getLocalizedProductCategories(" ", "<br>") . '</span>';
            $row['productName'] = $product->productNameTranslation->getFirst() ? $product->productNameTranslation->getFirst()->name : "";
            $row['colorNameManufacturer'] = $product->productVariant->description;
            $row['colorGroup'] = '<span class="small">' . (!is_null($product->productColorGroup) ? $product->productColorGroup->productColorGroupTranslation->getFirst()->name : "[Non assegnato]") . '</span>';
            $row['shop'] = '<span class="small">'.$product->getShops('<br />', true).'</span>';
            $row['hasDetails'] = (2 < $product->productSheetActual->count()) ? 'sì' : 'no';

            /** @var CProductDescriptionTranslation $descT */
            $descT = $product->productDescriptionTranslation->findOneByKeys(['marketplaceId'=>1, 'langId'=>1]);
            $row['pDescTranslate'] = (is_null($descT ) ? '-' : $descT->description);

            $datatable->setResponseDataSetRow($key,$row);
        }


        return $datatable->responseOut();

    }

}