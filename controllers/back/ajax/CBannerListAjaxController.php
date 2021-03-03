<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;

/**
 * Class CBannerListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 02/03/2021
 * @since 1.0
 */
class CBannerListAjaxController extends AAjaxController
{

    public function get()
    {
        $editBannerLink = "/blueseal/marketing/banner-modifica";
        $sql = "SELECT
                  `Banner`.`id`          AS `id`,
                  `Banner`.`name`        AS `name`,
                  `Banner`.`textHtml` AS `textHtml`,
                  `Banner`.`position` as `position`,
                  `Banner`.`link` as `link`,  
                  if(`Banner`.`isActive`,'Si','No') as isActive,  
                  `Banner`.`click`       AS `click`,
                  `Campaign`.`name`   AS `campaignName`,
                  `Shop`.`name`     AS `shopName`
                  
                FROM `Banner`
                  JOIN `Shop` ON `Banner`.`remoteShopId` = `Shop`.`id`
                  JOIN `Campaign` ON `Banner`.`campaignId` = `Campaign`.`id`";
        $datatable = new CDataTables($sql,['id'],$_GET,true);

        if (!empty($this->authorizedShops)) {
            $datatable->addCondition('shopId',$this->authorizedShops);
        }
        $repo = \Monkey::app()->repoFactory->create('Banner');
        $campaignRepo=\Monkey::app()->repoFactory->create('Campaign');
        $shopRepo=\Monkey::app()->repoFactory->create('Shop');
        $datatable->doAllTheThings(true);
        foreach($datatable->getResponseSetData() as $key=>$row) {
            $banner = $repo->findOneBy($row);
            $row["DT_RowId"] = 'row__'.$banner->id;
            $row["DT_RowClass"] = 'colore';
            $row['name'] = '<a data-toggle="tooltip" title="modifica" data-placement="right" href="'.$editBannerLink.'/'.$banner->id.'" >'.$banner->name.'</a>';
            $row['textHtml'] = '<img width="150px" src="'.$banner->textHtml.'"/>';
            $campaign=$campaignRepo->findOneBy(['id'=>$banner->campaignId]);
            $row['campaignName']=$campaign->name;
            $row['click']=$banner->click;
            $row['link']=$banner->link;
            $row['isActive']=($banner->isActive==1)?'Si':'No';
            $shop=$shopRepo->findOneBy(['id'=>$banner->remoteShopId]);
            $row['shopName']=$shop->name;
            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();
    }
}