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

    public
    function get()
    {
        if (isset($_REQUEST['detaillabelid'])) {
            $detailLabelId = $_REQUEST['detaillabelid'];
        }else{
                $detailLabelId='notSet';
        }
            if (isset($_REQUEST['selectdefine'])) {
                $selectDefine = $_REQUEST['selectdefine'];
            } else {
                $selectDefine = '';
            }
            if (isset($_REQUEST['textdefine'])) {
                $textDefine = $_REQUEST['textdefine'];
            } else {
                $textDefine = '';
            }
            if ($detailLabelId != 'notSet') {
                $sqlfilter = 'where 1=1 and details ';
                if ($selectDefine == 1) {
                    $sqlfilter = $sqlfilter . ' like \'%'.$detailLabelId.': ' . $textDefine . '%\'';
                } else {
                    $sqlfilter = $sqlfilter . ' not like \''.$detailLabelId.': ' . $textDefine . '\'';
                }
            } else {
                $sqlfilter = '';
            }


            $sql = "SELECT 
                    id,
                    modelCode as code,
                    modelName as name,
                    productName,
                    prototypeName,
                    categoryName as categories,
                    details,
                    if(catGroupName is not null,catGroupName,'Non Assegnato') as catGroupName,
                    if(gendName is not null,gendName,'Non Assegnato') as gendName,
                    if(matName is not null,matName,'Non Assegnato') as matName
                    FROM ProductSheetModelPrototypeSupport " .$sqlfilter;

            $datatable = new CDataTables($sql,['id'],$_GET,true);

            $datatable->doAllTheThings();

            $psmpR = \Monkey::app()->repoFactory->create('ProductSheetModelPrototypeSupport');
            $psmpmrR=\Monkey::app()->repoFactory->create('ProductSheetModelPrototypeMacroCategoryGroup');
        $psmcmrR=\Monkey::app()->repoFactory->create('ProductSheetModelPrototypeCategoryGroup');
            $modifica = $this->app->baseUrl(false) . "/blueseal/prodotti/modelli/modifica";
            foreach ($datatable->getResponseSetData() as $key => $row) {
                $psmps = \Monkey::app()->repoFactory->create('ProductSheetModelPrototype');
                $val = $psmpR->findOne([$row['id']]);


                $row["DT_RowId"] = 'row__' . $val->id;
                $row["id"] = $val->id;
                $row['code'] = $val->modelCode;
                $row['code'] = '<a data-toggle="tooltip" title="modifica" data-placement="right" href="' . $modifica . '?id=' . $val->id . '">' . $val->modelCode . '</a><br />';
                $row['code'] .= '<span class="small">(<a data-toggle="tooltip" title="Usa come modello" data-placement="right" href="' . $modifica . '?modelId=' . $val->id . '">Usa come modello</a>)</span><br />';
                $row['name'] = $val->modelName;
                $row['productName'] = $val->productName;
                $row['prototypeName'] = $val->prototypeName;
                $model = $psmps->findOneBy(['id' => $val->id]);
                $row['prototypeId'] = $model->productSheetPrototype->id;
                $cats = '<span class="small">';
                foreach ($model->productCategory as $cat) {
                    $cats .= $cat->getLocalizedName() . "<br />";
                }
                $cats .= '</span>';
                $row['categories'] = $cats;
                unset($cats);
                $categoryGroupId=$model->categoryGroupId;
                $macroCategory='';
                $urlImageMacroCategory='';
                $urlImageCategory='';
                if($categoryGroupId!=null) {
                    $findCategoryGroup = $psmcmrR->findOneBy(['id' => $categoryGroupId]);
                    $urlImageCategory=(is_null($findCategoryGroup->imageUrl)? ' ':'<img width="50px" src="'.$findCategoryGroup->imageUrl.'"</img>');
                    $findMacroCategory = $psmpmrR->findOneBy(['id' => $findCategoryGroup->macroCategoryGroupId]);
                    $macroCategory=$findMacroCategory->name;
                    $urlImageMacroCategory=($findMacroCategory->imageUrl=='') ? '': '<img width="50px" src="'.$findMacroCategory->imageUrl.'"</img>';
                }

                $row['imageUrlCategory']=$urlImageCategory;
                $row['macroCategory']=$macroCategory;
                $row['imageUrlMacroCategory']=$urlImageMacroCategory;
                $row['details'] = $val->details;
                $row['catGroupName'] = (is_null($val->catGroupName) ? 'Non Assegnato' : $val->catGroupName);
                $row['gendName'] = (is_null($val->gendName) ? 'Non Assegnato' : $val->gendName);
                $row['matName'] = (is_null($val->matName) ? 'Non Assegnato' : $val->matName);

                $datatable->setResponseDataSetRow($key,$row);
            }


            return $datatable->responseOut();
        }


}