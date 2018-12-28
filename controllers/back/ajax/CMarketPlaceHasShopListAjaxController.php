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



class CMarketPlaceHasShopListAjaxController extends AAjaxController
{

    public function get()
    {

        //$campaignId = \Monkey::app()->router->request()->getRequestData('campaignid');

        $sql = "SELECT 
                      mhs.id as id, 
                      s.name as shopName , 
                      ma.name as markeplaceName,  
                      mhs.typeSync as typeSync,
                      mhs.imgMarketPlace as imgMarketPlace,
                      mhs.prestashopId as prestashopId
                      
                FROM MarketplaceHasShop mhs 
                inner join Marketplace ma on mhs.marketPlaceId=ma.id
                inner join Shop s on mhs.shopId=s.id";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings('true');
        $imgMarketPlacePath=\Monkey::app()->baseUrl(FALSE)."/images/imgorder/";
        $blueseal = $this->app->baseUrl(false) . '/blueseal/';
        $opera = $blueseal . "prodotti/marketplace/associate/sale?id=";
        foreach ($datatable->getResponseSetData() as $key => $row) {
            $row['id'] = '<a href="' . $opera .  $row['id'] . '">' .  $row['id'] . '</a>';
        if($row['typeSync']==1){
            $row['typeSync']='automatico';

        }else{
            $row['typeSync']='manuale';
        }
        $image=$row['imgMarketPlace'];
        $row['imgMarketPlace']="<img width='80' src='".$imgMarketPlacePath.$image."'</img>";

            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();
    }
}