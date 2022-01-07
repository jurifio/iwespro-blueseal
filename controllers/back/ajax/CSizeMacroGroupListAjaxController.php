<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;


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
class CSizeMacroGroupListAjaxController extends AAjaxController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function get()
    {
        $sql = "SELECT  pmg.id,
                        pmg.name,
                        psg.name as productSizeGroupName,
                        group_concat(DISTINCT(ps.name)) as sizes,
                        psg.locale,
                        psg.id as idGroupSize,
                        psg.publicName,
                        psg.country as country,
                        psg.category as category
                FROM ProductSizeMacroGroup pmg
                LEFT JOIN ProductSizeGroup psg ON pmg.id = psg.productSizeMacroGroupId
                LEFT JOIN ProductSizeGroupHasProductSize psghps ON psg.id = psghps.productSizeGroupId
                LEFT JOIN ProductSize ps ON psghps.productSizeId = ps.id
                GROUP BY psg.id, pmg.id";

        $datatable = new CDataTables($sql, ['idGroupSize', 'id'], $_GET, true);

        $datatable->doAllTheThings(true);

        $blueseal = $this->app->baseUrl(false).'/blueseal/';
        $url = $blueseal."prodotti/gruppo-taglie/aggiungi";
        $countryRepo=\Monkey::app()->repoFactory->create('Country');
        $categoryRepo=\Monkey::app()->repoFactory->create('ProuductCategory');

        foreach ($datatable->getResponseSetData() as $key=>$row) {
            $categoryInsert="";
            $countryInsert='';
            $categoryText='';
            $categoryName='';
            if(empty($row['idGroupSize'])){
                $row["DT_RowClass"] = "red";
                $row['idGroupSize'] = "---";
                $row['productSizeGroupName'] = "Macrogruppo collegato a nessun gruppo";
                $row['sizes'] = "Macrogruppo collegato a nessun gruppo";
                $row['locale'] = "Macrogruppo collegato a nessun gruppo";
                $row['modifica'] = "<i class='fa fa-exclamation'></i>";



            } else {
                $row['modifica'] = "<a href='".$url . "?productSizeGroupId=" . $row['idGroupSize'] . " '><i class='fa fa-pencil-square-o'></i></a>";
                $row['sizes'] = str_replace(',',' | ',$row['sizes']);
                $countryText='';
                $countrys=[];

                if(!is_null($row['country']) ||  $row['country']!=''){
                    $countrys=explode(',',$row['country']);
                    $i=0;
                    foreach ($countrys as $country){
                        $countryFind=$countryRepo->findOneBy(['id'=>$country]);
                        if($countryFind) {
                           $countryInsert=$countryInsert.$countryFind->name.'<br>';
                            $i++;
                        }
                    }
                    $row['country']=$countryInsert;
                }
                if(!is_null($row['category']) ||  $row['category']!=''){
                    $categories=explode(',',$row['category']);
                    $i=0;
                    foreach ($categories as $category){

                            $sqlCategory = 'SELECT t0.id as  id,t0.slug as slug
      ,(SELECT GROUP_CONCAT(t2.slug)
                    FROM ProductCategory t2
                    WHERE t2.lft<t0.lft AND t2.rght>t0.rght
                    ORDER BY t2.lft) ancestors

                    FROM ProductCategory  t0 where id=' . $category . ' GROUP BY t0.slug';
                            $res_category = \Monkey::app()->dbAdapter->query($sqlCategory,[])->fetchAll();

                            foreach ($res_category as $scategory) {
                                $categoryName = str_replace(',','/',($scategory['ancestors'] . ',' . $scategory['slug']));

                            }
                            $categoryInsert=$categoryInsert.'<br>'.$categoryName;
                            $i++;
                        }
                    }
                    $row['category']=$categoryInsert;

            }





            $datatable->setResponseDataSetRow($key,$row);
        }


        return $datatable->responseOut();

    }

}