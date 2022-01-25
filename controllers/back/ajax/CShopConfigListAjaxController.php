<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CShop;
use bamboo\domain\entities\CShopConfigProd;
use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\entities\AEntity;
use bamboo\domain\entities\CShopConfigDev;


/**
 * Class CShopConfigListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 20/01/2022
 * @since 1.0
 */
class CShopConfigListAjaxController extends AAjaxController
{
    public function get()
    {
        if(ENV=='dev') {
        $sql = "SELECT
                  scf.id as id,
                  scf.shopId as shopId,
                  `s`.`name` as shopName,
                   s.urlSite as urlSite,
                   s.dbHost as dbHost,
                   s.dbUsername as dbUsername,
                   s.dbPassword as dbPassword,
                   s.dbName as dbName,
                   if(s.isActive=1,'Attivo','Non Attivo') as isActive
                FROM ShopConfigDev scf 
                  JOIN Shop s ON scf.shopId = s.id
                  
                GROUP BY scf.id
               ";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

          //  $datatable->addCondition('shopId',\Monkey::app()->repoFactory->create('Shop')->getAutorizedShopsIdForUser());
        }else{
            $sql = "SELECT
                  scf.id as id,
                  scf.shopId as shopId,
                  `s`.`name` as shopName,
                   s.urlSite as urlSite,
                   s.dbHost as dbHost,
                   s.dbUsername as dbUsername,
                   s.dbPassword as dbPassword,
                   s.dbName as dbName,
                   if(s.isActive=1,'Attivo','Non Attivo') as isActive
                FROM ShopConfigProd scf 
                  JOIN Shop s ON scf.shopId = s.id
                  
                GROUP BY scf.id
               ";

            $datatable = new CDataTables($sql, ['id'], $_GET, true);
           // $datatable->addCondition('shopId',\Monkey::app()->repoFactory->create('Shop')->getAutorizedShopsIdForUser());
        }

        $datatable->doAllTheThings(false);

        $blueseal = $this->app->baseUrl(false).'/blueseal/';
        $url = $blueseal."impostazioni/shop/configurazioni/";


        $sRepo = \Monkey::app()->repoFactory->create('Shop');
        if(ENV=='dev') {
            $configRepo = \Monkey::app()->repoFactory->create('ShopConfigDev');
        }else{
            $configRepo = \Monkey::app()->repoFactory->create('ShopConfigProd');
        }



        foreach ($datatable->getResponseSetData() as $key=>$row) {

            if(ENV=='dev'){
                /** @var CShopConfigDev $configShop */
            }else{
                /** @var CShopConfigProd $configShop */
            }
            /** @var CShop $shop */
        $configShop=$configRepo->findOneBy(['id'=>$row['id']]);

            $shop = $sRepo->findOneBy(['id'=>$configShop->shopId]);
            $row["id"]='<a href="' . $url . $configShop->id.'">' . $configShop->id . '</a>';
            $row["row_id"] =  $configShop->id;
            $row["shopName"] = $shop->name;
            $row['isActive'] =($shop->isActive==1)?'Attivo':'non Attivo';

            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();

    }
}