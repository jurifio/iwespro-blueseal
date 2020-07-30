<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;


/**
 * Class CProductModelRevertListSupportAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 29/07/2020
 * @since 1.0
 */
class CProductModelRevertListSupportAjaxController extends AAjaxController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function get()
    {
        $sql = "SELECT 
                    id,
                    psmp.modelCode as code,
                    psmp.name as name,
                    psmp.productName as productName,K
                    pp.name as prototypeName,
                    PSMPCG.name as categories,
                    details,
                    catGroupName,
                    gendName,
                    matName
                        FROM ProductSheetModelPrototype psmp join ProductSheetPrototype pp on psmp.productSheetPrototypeId=pp.id
                        left join ProductSheetModelPrototypeCategoryGroup PSMPCG on psmp.categoryGroupId = PSMPCG.id 
                        left join ProductSheetModelPrototypeMacroCategoryGroup PSMPMCG on PSMPCG.macroCategoryGroupId = PSMPMCG.id
                        left join ProductSheetModelPrototypeMaterial psm on psmp.materialId=psm.id
                        left join ProductSheetModelPrototypeGender psg on psm.genderId=psg.id


                        ";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings();

        $psmpR = \Monkey::app()->repoFactory->create('ProductSheetModelPrototype');
        $modifica = $this->app->baseUrl(false)."/blueseal/prodotti/modelli/modifica";
        foreach ($datatable->getResponseSetData() as $key=>$row) {
            
            $val = $psmpR->findOne([$row['id']]);


            $row["DT_RowId"] = 'row__'.$val->id;
            $row["id"] = $val->id;
            $row['code'] = $val->code;
            $row['code'] = '<a data-toggle="tooltip" title="modifica" data-placement="right" href="'.$modifica.'?id='.$val->id.'">'.$val->code.'</a><br />';
            $row['code'].= '<span class="small">(<a data-toggle="tooltip" title="Usa come modello" data-placement="right" href="'.$modifica.'?modelId='.$val->id.'">Usa come modello</a>)</span><br />';
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