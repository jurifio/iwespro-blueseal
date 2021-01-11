<?php


namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CNewsletterEvent;
use bamboo\domain\entities\CNewsletterUser;
use bamboo\blueseal\remote\readextdbtable\CReadExtDbTable;


/**
 * Class CAggregatorHasShopListAjaxController
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



class CAggregatorHasShopListAjaxController extends AAjaxController
{

    public function get()
    {

        //$campaignId = \Monkey::app()->router->request()->getRequestData('campaignid');

        $sql = "SELECT 
                      mhs.id as id, 
                      s.name as shopName , 
                      ma.name as markeplaceName,  
                      mhs.typeSync as typeSync,
                      mhs.imgAggregator as imgAggregator,
                      if(mhs.isPriceHub='1','Si','No') as priceRule 
                      
                FROM AggregatorHasShop mhs 
                inner join Marketplace ma on mhs.marketplaceId=ma.id
                inner join Shop s on mhs.shopId=s.id";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings('true');
        $imgMarketPlacePath=\Monkey::app()->baseUrl(FALSE)."/images/imgorder/";
        $blueseal = $this->app->baseUrl(false) . '/blueseal/';
        $opera = $blueseal . "aggregatori/account-shop/modifica/";
        foreach ($datatable->getResponseSetData() as $key => $row) {
            $row['DT_RowId']=$row['id'];
            $row['id'] = '<a href="' . $opera .  $row['id'] . '">' .  $row['id'] . '</a>';
        if($row['typeSync']==1){
            $row['typeSync']='automatico';

        }else{
            $row['typeSync']='manuale';
        }

        $image=$row['imgAggregator'];
        $row['imgAggregator']="<img width='80' src='".$image."'</img>";

            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();
    }
}