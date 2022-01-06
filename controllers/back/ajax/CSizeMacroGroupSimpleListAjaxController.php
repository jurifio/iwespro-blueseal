<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CProductSize;
use bamboo\domain\entities\CProductSizeGroup;
use bamboo\domain\entities\CProductSizeMacroGroup;
use bamboo\domain\entities\CProductCategory;
use bamboo\domain\entities\CProductSizeMacroGroupHasProductCategory;
use bamboo\domain\repositories\CProductSizeRepo;
use bamboo\domain\entities\CProduct;


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
                  GROUP_CONCAT(psg.id) as productSizeGroups,
                 GROUP_CONCAT(psmghpc.productCategoryId) as productCategoryGroups
                FROM ProductSizeMacroGroup psm
                LEFT JOIN ProductSizeGroup psg ON psm.id = psg.productSizeMacroGroupId        
                left join ProductSizeMacroGroupHasProductCategory psmghpc on psm.id = psmghpc.productSizeMacroGroupId            
                GROUP BY psm.id";

        $datatable = new CDataTables($sql,['id'],$_GET,true);

        $datatable->doAllTheThings(false);

        /** @var CProductSizeMacroGroup $productSizeGroupRepo */
        $productSizeGroupRepo = \Monkey::app()->repoFactory->create('ProductSizeMacroGroup');

        foreach ($datatable->getResponseSetData() as $key => $row) {

            /** @var CProductSizeMacroGroup $productSizeMacroGroup */
            $productSizeMacroGroup = $productSizeGroupRepo->findOne([$row['id']]);

            $row['id'] = $productSizeMacroGroup->id;

            $row['name'] = $productSizeMacroGroup->name;

            /** @var CProductSizeGroup $allGroupSize */
            $allGroupSize = $productSizeMacroGroup->productSizeGroup->findByKey('productSizeMacroGroupId',$row['id']);

            if (!empty($allGroupSize)) {
                foreach ($allGroupSize as $singleProductSizeGroup) {
                    $row['productSizeGroups'][] = " ," . $singleProductSizeGroup->name;
                }
            } else {
                $row['productSizeGroups'] = "---";
            }
            /** @var CProductSizeMacroGroupHasProductCategory $allProductCategory */
            $allProductCategory = \Monkey::app()->repoFactory->create('ProductSizeMacroGroupHasProductCategory')->findBy(['productSizeMacroGroupId' => $row['id']]);
            if (!empty($allProductCategory)) {
                foreach ($allProductCategory as $singleProductCategory) {
                    $collectCategory = '';
                    $sqlCategory = 'SELECT t0.id as  id,t0.slug as slug
      ,(SELECT GROUP_CONCAT(t2.slug)
                    FROM ProductCategory t2
                    WHERE t2.lft<t0.lft AND t2.rght>t0.rght
                    ORDER BY t2.lft) ancestors

                    FROM ProductCategory  t0 where id=' . $singleProductCategory->productCategoryId . ' GROUP BY t0.slug';
                    $res_category = \Monkey::app()->dbAdapter->query($sqlCategory,[])->fetchAll();

                    foreach ($res_category as $category) {
                        $categoryName = str_replace(',','/',($category['ancestors'] . ',' . $category['slug']));

                    }

                    $row['productCategoryGroups'][] = '<br>'.$categoryName;

                    //$row['productCategoryGroups'][] = " ,".$singleProductCategory->productCategoryId;
                }

            } else {
                $row['productCategoryGroups'] = "---";
            }


            $datatable->setResponseDataSetRow($key,$row);
        }


        return $datatable->responseOut();
    }
}