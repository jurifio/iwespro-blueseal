<?php


namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CNewsletterEvent;
use bamboo\domain\entities\CNewsletterUser;
use bamboo\blueseal\remote\readextdbtable\CReadExtDbTable;


/**
 * Class CMarketPlaceHasShopListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 20/09/2018
 * @since 1.0
 */



class CMarketPlaceHasNewShopListAjaxController extends AAjaxController
{

    public function get()
    {

        //$campaignId = \Monkey::app()->router->request()->getRequestData('campaignid');

        $sql = "SELECT 
                       m.id as marketplaceId, 
                       concat(ma.id,'-',ma.marketplaceId) AS `code`, 
                       ma.id as id, 
                      `m`.`name` as marketplaceName , 
                      `m`.`icon` as icon,
                      `m`.`type` AS `type`,
                      `ma`.`id`  as marketplaceAccountId,
                      `ma`.`name` as marketplaceAccountName,
                      `ma`.`urlSite` as urlSite,
                      `ma`.`config` as config,
                       if(`ma`.isActive=1,'Si','No') as isActive 
                      
                FROM MarketplaceAccount ma 
                 join Marketplace m on ma.marketplaceId=m.id
                 where m.`type`='marketplace'";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings('true');
        $blueseal = $this->app->baseUrl(false) . '/blueseal/';
        $opera = $blueseal . "marketplace/marketplace-shop/modifica/";
        foreach ($datatable->getResponseSetData() as $key => $row) {
            $row['DT_RowId']=$row['id'];
            $row['DT_RowCode']=$row['id'].'-'.$row['marketplaceId'];
            $row['code'] = '<a href="' . $opera .  $row['id'] . '">' .  $row['id'] . '</a>';

            $config=json_decode($row['config']);
            $img='';
            if(isset($config->logoFile)){
                $img='<img width="50" src="' . $config->logoFile . '" /></a>';
            }
            $row['img']=$img;

            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();
    }
}