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
	    $sample = $this->app->repoFactory->create('MarketplaceAccountCategory')->getEmptyEntity();

        $sql = "select `m`.`id` AS `marketplaceId`,`ma`.`id` AS `marketplaceAccountId`,`mac`.`marketplaceCategoryId` AS `marketplaceCategoryId`,`m`.`name` AS `marketplace`,`ma`.`name` AS `marketplaceAccount`,`mac`.`name` AS `marketplaceAccountCategory`,`mac`.`path` AS `marketplaceAccountPath`,`mac`.`isRelevant` AS `isRelevant`,`pc`.`slug` AS `internalCategory`,`mac`.`name` AS `marketplaceCategory` from ((((`Marketplace` `m` join `MarketplaceAccount` `ma` on((`m`.`id` = `ma`.`marketplaceId`))) join `MarketplaceAccountCategory` `mac` on(((`ma`.`marketplaceId` = `mac`.`marketplaceId`) and (`ma`.`id` = `mac`.`marketplaceAccountId`)))) left join `ProductCategoryHasMarketplaceAccountCategory` `pchmac` on(((`mac`.`marketplaceAccountId` = `pchmac`.`marketplaceAccountId`) and (`mac`.`marketplaceId` = `pchmac`.`marketplaceAccountId`) and (`mac`.`marketplaceCategoryId` = `pchmac`.`marketplaceAccountCategoryId`)))) left join `ProductCategory` `pc` on((`pc`.`id` = `pchmac`.`productCategoryId`)))";
        $datatable = new CDataTables($sql,$sample->getPrimaryKeys(),$_GET,true);
		$datatable->addCondition('isRelevant',[1]);

        $marketplaceCategories = $sample->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $sample->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $sample->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $okManage = $this->app->getUser()->hasPermission('/admin/product/edit');

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $i = 0;
        foreach($marketplaceCategories as $val) {
	        $response['data'][$i]["DT_RowId"] = 'row__'.$val->printId();
            $response['data'][$i]['marketplace'] = $val->marketplaceAccount->marketplace->name;
            $response['data'][$i]['marketplaceAccount'] = $val->marketplaceAccount->name;
            $response['data'][$i]['marketplaceAccountCategory'] = $val->name;
			try {
				$appoggio = explode('_',$val->path);
				unset($appoggio[0]);
				$appoggio = array_reverse($appoggio);
				$appoggio = implode('<br>',$appoggio);
			} catch (\Throwable $e) {
				$appoggio = $val->marketplaceCategoryPath;
			}
	        $response['data'][$i]['marketplaceAccountPath'] = $appoggio;
	        if(!$okManage) {
				$html = 'Non si può';
	        } else {
	        	$catIds = [];
	        	foreach($val->productCategory as $productCategory) {
					$catIds[] = $productCategory->id;
		        }
		        $html = '<select class="full-width selectpicker selectize-streachable" 
		                         placeholder="Seleziona la categoria"
		                         data-name="categorySelect"
		                         data-selected="'.implode('__',$catIds).'"
		                         data-id="' . $val->printId() . '" 
		                         tabindex="-1" ></select>';
	        }
            $response['data'][$i]['internalCategory'] = $html;
            $i++;
        }
        return json_encode($response);
    }
}