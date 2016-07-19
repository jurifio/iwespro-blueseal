<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;

/**
 * Class CMarketplaceProductListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 18/07/2016
 * @since 1.0
 */
class CMarketplaceProductListAjaxController extends AAjaxController
{
    public function get()
    {
	    if ($this->app->getUser()->hasPermission('allShops')) {

	    } else{
		    $res = $this->app->dbAdapter->select('UserHasShop',['userId'=>$this->app->getUser()->getId()])->fetchAll();
		    foreach($res as $val) {
			    $authorizedShops[] = $val['shopId'];
		    }
	    }

	    $sample = $this->app->repoFactory->create('Product')->getEmptyEntity();

        $datatable = new CDataTables('vBluesealMarketplaceProductList',$sample->getPrimaryKeys(),$_GET);
        if(!empty($authorizedShops)){
            $datatable->addCondition('shopId',$authorizedShops);
        }

        $prodotti = $sample->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $sample->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $sample->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $i = 0;
        foreach($prodotti as $val){

	        $img = strpos($val->dummyPicture,'s3-eu-west-1.amazonaws.com') ? $val->dummyPicture : "/assets/".$val->dummyPicture;
	        if($val->productPhoto->count() > 3) $imgs = '<br><i class="fa fa-check" aria-hidden="true"></i>';
	        else $imgs = "";

	        $shops = [];
	        foreach ($val->shop as $shop) {
		        $shops[] = $shop->name;
	        }


	        $response['data'][$i]["DT_RowId"] = $val->printId();
            $response['data'][$i]["DT_RowClass"] = 'colore';
            $response['data'][$i]['code'] = $val->printId();
	        $response['data'][$i]['shop'] = implode(', ',$shops);
	        $response['data'][$i]['dummy'] = '<img width="50" src="'.$img.'" />' . $imgs . '<br />';
            $response['data'][$i]['itemno'] = '<span class="small">';
            $response['data'][$i]['itemno'] .= $val->itemno.' # '.$val->productVariant->name;
            $response['data'][$i]['itemno'] .= '</span>';

	        $response['data'][$i]['marketplaceAccountName'] = 'tante cose';

            $i++;
        }

        return json_encode($response);
    }
}