<?php
namespace bamboo\controllers\back\ajax;

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
    public function get()
    {
	    $marketplaceAccountCategoryRepo =  \Monkey::app()->repoFactory->create('MarketplaceAccountCategory');

        $sql = "select `m`.`id` AS `marketplaceId`,
                       `ma`.`id` AS `marketplaceAccountId`,
                       `mac`.`marketplaceCategoryId` AS `marketplaceCategoryId`,
                       `m`.`name` AS `marketplace`,
                       `ma`.`name` AS `marketplaceAccount`,
                       `mac`.`name` AS `marketplaceAccountCategory`,
                       `mac`.`path` AS `marketplaceAccountPath`,
                       `mac`.`isRelevant` AS `isRelevant`,
                       `pc`.`slug` AS `internalCategory`,
                       pc.id as productCategoryId,
                       `mac`.`name` AS `marketplaceCategory` 
               from `Marketplace` `m` 
                    join `MarketplaceAccount` `ma` on `m`.`id` = `ma`.`marketplaceId` 
                    join `MarketplaceAccountCategory` `mac` on (`ma`.`marketplaceId`,`ma`.`id`) = (`mac`.`marketplaceId`,`mac`.`marketplaceAccountId`)
                    left join (`ProductCategoryHasMarketplaceAccountCategory` `pchmac` 
                                join `ProductCategory` `pc` on `pc`.`id` = `pchmac`.`productCategoryId`)
                              on (`mac`.`marketplaceId`,`mac`.`marketplaceAccountId`, `mac`.`marketplaceCategoryId` ) = 
                                  (`pchmac`.`marketplaceId`,`pchmac`.`marketplaceAccountId`, `pchmac`.`marketplaceAccountCategoryId`)";

        $datatable = new CDataTables($sql,$marketplaceAccountCategoryRepo->getEmptyEntity()->getPrimaryKeys(),$_GET,true);
		$datatable->addCondition('isRelevant',[1]);

        $okManage = $this->app->getUser()->hasPermission('/admin/product/edit');

        $datatable->doAllTheThings(true);

        foreach($datatable->getResponseSetData() as $key => $row) {
            $val = $marketplaceAccountCategoryRepo->findOne($row);
            $row["DT_RowId"] = 'row__'.$val->printId();
            $row['marketplace'] = $val->marketplaceAccount->marketplace->name;
            $row['marketplaceAccount'] = $val->marketplaceAccount->name;
            $row['marketplaceAccountCategory'] = $val->name;
			try {
				$appoggio = explode('_',$val->path);
				unset($appoggio[0]);
				$appoggio = array_reverse($appoggio);
				$appoggio = implode('<br>',$appoggio);
			} catch (\Throwable $e) {
				$appoggio = $val->marketplaceCategoryPath;
			}
            $row['marketplaceAccountPath'] = $appoggio;
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
            $row['productCategoryId'] = $html;
            $datatable->setResponseDataSetRow($key,$row);
        }
        return $datatable->responseOut();
    }
}