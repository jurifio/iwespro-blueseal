<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;


/**
 * Class CProductModelListSupportAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 19/10/2018
 * @since 1.0
 */
class CProductModelListSupportAjaxController extends AAjaxController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function get()
    {
        $sql = "SELECT 
                    id,
                    modelCode as code,
                    modelName as name,
                    productName,
                    prototypeName,
                    categoryName as categories,
                    details,
                    catGroupName,
                    gendName,
                    matName
                    FROM ProductSheetModelPrototypeSupport";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings();

        $psmpR = \Monkey::app()->repoFactory->create('ProductSheetModelPrototype');

        foreach ($datatable->getResponseSetData() as $key=>$row) {
            
            $val = $psmpR->findOne([$row['id']]);


            $row["DT_RowId"] = 'row__'.$val->id;
            $row["id"] = $val->id;
            $row['code'] = $val->code;
            $row['name'] = $val->name;
            $row['productName'] = $val->productName;
            $row['prototypeName'] = $val->productSheetPrototype->name;
            $row['prototypeId'] = $val->productSheetPrototype->id;
            $cats = '<span class="small">';
            foreach ($val->productCategory as $cat) {
                $cats.= $cat->getLocalizedName() . "<br />";
            }
            $cats.= '</span>';
            $row['categories'] = $cats;
            unset($cats);

            $row['details'] = '<span class="small">';
            foreach ($val->productSheetModelActual as $det) {
                $row['details'] .=
                    $det->productDetailLabel->slug .
                    ":" .
                    $det->productDetail->productDetailTranslation->getFirst()->name .
                    '<br />';
            }
            $row['details'].= '</span>';
            $row['catGroupName'] = (is_null($val->categoryGroupId) ?  '-' : $val->productSheetModelPrototypeCategoryGroup->name);
            $row['gendName'] = (is_null($val->genderId) ? '-' : $val->productSheetModelPrototypeGender->name);
            $row['matName'] = (is_null($val->materialId) ? '-' : $val->productSheetModelPrototypeMaterial->name);

            $datatable->setResponseDataSetRow($key,$row);
        }


        return $datatable->responseOut();
    }
}