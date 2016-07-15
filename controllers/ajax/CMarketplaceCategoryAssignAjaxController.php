<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;

/**
 * Class CMarketplaceCategoryAssignAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 12/07/2016
 * @since 1.0
 */
class CMarketplaceCategoryAssignAjaxController extends AAjaxController
{
    protected $urls = [];
    protected $authorizedShops = [];
    protected $em;

    public function get()
    {
        $datatable = new CDataTables('vBluesealMarketplaceCategory',['marketplaceId','marketplaceCategoryId'],$_GET);
		$datatable->addCondition('isRelevant',[1]);
        $marketplaceCategories = $this->app->repoFactory->create('MarketplaceCategoryLookup')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->app->repoFactory->create('MarketplaceCategoryLookup')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->app->repoFactory->create('MarketplaceCategoryLookup')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $okManage = $this->app->getUser()->hasPermission('/admin/product/edit');

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];



        $i = 0;
        foreach($marketplaceCategories as $val) {
	        $response['data'][$i]["DT_RowId"] = 'row__'.$val->marketplaceId.'-'.$val->marketplaceCategoryId;
            $response['data'][$i]['marketplace'] = $val->marketplace->name;
            $response['data'][$i]['marketplaceCategory'] = $val->marketplaceCategoryName;
			try {
				$appoggio = explode('_',$val->marketplaceCategoryPath);
				unset($appoggio[0]);
				$appoggio = array_reverse($appoggio);
				$appoggio = implode('<br>',$appoggio);
			} catch (\Exception $e) {
				$appoggio = $val->marketplaceCategoryPath;
			}
	        $response['data'][$i]['marketplaceCategoryDescription'] = $appoggio;
	        if(!$okManage) {
				$html = 'Non si può';
	        } else {
		        $html = '<select class="full-width selectpicker" 
		                         placeholder="Seleziona la categoria"
		                        
		                         data-name="categorySelect"
		                         data-selected="'.$val->productCategoryId.'"
		                         data-id="' . $val->marketplaceId.'-'.$val->marketplaceCategoryId . '" 
		                         tabindex="-1" ></select>';
	        }
            $response['data'][$i]['internalCategory'] = $html;
            $i++;
        }
        return json_encode($response);
    }
}