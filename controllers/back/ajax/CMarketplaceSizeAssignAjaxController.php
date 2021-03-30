<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;

/**
 * Class CMarketplaceSizeAssignAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 30/03/2021
 * @since 1.0
 */
class CMarketplaceSizeAssignAjaxController extends AAjaxController
{
    public function get()
    {
	    $marketplaceAccountSizeRepo =  \Monkey::app()->repoFactory->create('MarketplaceAccountSize');

        $sql = "select `m`.`id` AS `marketplaceId`,
                       `ma`.`id` AS `marketplaceAccountId`,
                       `mas`.`marketplaceSizeId` AS `marketplaceSizeId`,
                        `mas`.`productType` as productType,
                       `m`.`name` AS `marketplace`,
                       `ma`.`name` AS `marketplaceAccount`,
                       `mas`.`name` AS `marketplaceAccountSize`,
                       `mas`.`path` AS `marketplaceAccountPath`,
                       `mas`.`isRelevant` AS `isRelevant`,
                       `ps`.`slug` AS `internalSize`,
                       ps.id as productSizeId,
                       `mas`.`name` AS `marketplaceSize` 
               from `Marketplace` `m` 
                    join `MarketplaceAccount` `ma` on `m`.`id` = `ma`.`marketplaceId` 
                     join `MarketplaceAccountSize` `mas` on (`ma`.`marketplaceId`,`ma`.`id`) = (`mas`.`marketplaceId`,`mas`.`marketplaceAccountId`)
                    left join (`ProductSizeHasMarketplaceAccountSize` `pchmac` 
                                join `ProductSize` `ps` on `ps`.`id` = `pchmac`.`productSizeId`)
                              on (`mas`.`marketplaceId`,`mas`.`marketplaceAccountId`, `mas`.`marketplaceSizeId`) = 
                                  (`pchmac`.`marketplaceId`,`pchmac`.`marketplaceAccountId`, `pchmac`.`marketplaceSizeId`)
                                  where m.type='marketplace'";

         $datatable = new CDataTables($sql,$marketplaceAccountSizeRepo->getEmptyEntity()->getPrimaryKeys(),$_GET,true);
		$datatable->addCondition('isRelevant',[1]);

        $okManage = $this->app->getUser()->hasPermission('/admin/product/edit');

        $datatable->doAllTheThings(true);

        foreach($datatable->getResponseSetData() as $key => $row) {
            $val = $marketplaceAccountSizeRepo->findOne($row);
            $row["DT_RowId"] = 'row__'.$val->printId();
            $row['marketplace'] = $val->marketplaceAccount->marketplace->name;
            $row['marketplaceAccount'] = $val->marketplaceAccount->name;
            $row['marketplaceAccountSize'] = $val->name;
			try {
				$appoggio = explode('_',$val->path);
				unset($appoggio[0]);
				$appoggio = array_reverse($appoggio);
				$appoggio = implode('<br>',$appoggio);
			} catch (\Throwable $e) {
				$appoggio = $val->marketplaceSizePath;
			}
            $row['marketplaceAccountPath'] = $appoggio;
	        if(!$okManage) {
				$html = 'Non si può';
	        } else {
	        	$catIds = [];
	        	foreach($val->productSize as $productSize) {
					$catIds[] = $productSize->id;
		        }
		        $html = '<select class="full-width selectpicker selectize-streachable" 
		                         placeholder="Seleziona la taglia"
		                         data-name="sizeSelect"
		                         data-selected="'.implode('__',$catIds).'"
		                         data-id="' . $val->printId() . '" 
		                         tabindex="-1" ></select>';
	        }
            $row['productSizeId'] = $html;
            $datatable->setResponseDataSetRow($key,$row);
        }
        return $datatable->responseOut();
    }
}