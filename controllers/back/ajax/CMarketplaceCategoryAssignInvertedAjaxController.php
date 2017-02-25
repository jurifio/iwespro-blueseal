<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CProductCategory;

/**
 * Class CMarketplaceCategoryAssignInvertedAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CMarketplaceCategoryAssignInvertedAjaxController extends AAjaxController
{
    protected $urls = [];
    protected $authorizedShops = [];
    protected $em;

    public function get()
    {
        $sql = "select `m`.`id` AS `marketplaceId`,`ma`.`id` AS `marketplaceAccountId`,`mac`.`marketplaceCategoryId` AS `marketplaceCategoryId`,`m`.`name` AS `marketplace`,`ma`.`name` AS `marketplaceAccount`,`mac`.`name` AS `marketplaceAccountCategory`,`mac`.`path` AS `marketplaceAccountPath`,`pc`.`slug` AS `internalCategory`,`pc`.`id` AS `productCategoryId`,`pc`.`slug` AS `productCategory` from ((((`ProductCategory` `pc` left join `MarketplaceAccount` `ma` on((1 = 1))) left join `Marketplace` `m` on((`m`.`id` = `ma`.`marketplaceId`))) left join `ProductCategoryHasMarketplaceAccountCategory` `pchmac` on(((`pc`.`id` = `pchmac`.`productCategoryId`) and (`ma`.`id` = `pchmac`.`marketplaceAccountId`) and (`ma`.`marketplaceId` = `pchmac`.`marketplaceId`)))) left join `MarketplaceAccountCategory` `mac` on(((`mac`.`marketplaceAccountId` = `pchmac`.`marketplaceAccountId`) and (`mac`.`marketplaceId` = `pchmac`.`marketplaceId`) and (`mac`.`marketplaceCategoryId` = `pchmac`.`marketplaceAccountCategoryId`)))) where (`pc`.`id` <> 1) group by `pc`.`id`,`ma`.`id`,`m`.`id`";
        $datatable = new CDataTables($sql,['marketplaceId','marketplaceAccountId','productCategoryId'],$_GET,true);

        $orribilità = $this->app->dbAdapter->query($datatable->getQuery(false,true),$datatable->getParams())->fetchAll();
        $count = $this->app->dbAdapter->query($datatable->getQuery(true,true),$datatable->getParams())->fetch();
        $totalCount = $this->app->dbAdapter->query($datatable->getQuery('full',true),$datatable->getParams())->fetch();
        $okManage = $this->app->getUser()->hasPermission('/admin/product/edit');
        $c = $datatable->getQuery(false,true);
        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        foreach($orribilità as $val) {
            $row = [];
            /** @var CProductCategory $productCategory */
            $productCategory = \Monkey::app()->repoFactory->create('ProductCategory')->findOne([$val['productCategoryId']]);
            $marketplaceAccount = \Monkey::app()->repoFactory->create('MarketplaceAccount')->findOne(['id'=>$val['marketplaceAccountId'],'marketplaceId'=>$val['marketplaceId']]);
            if(!is_null($val['marketplaceCategoryId'])){
                $marketplaceAccountCateogory = \Monkey::app()->repoFactory->create('MarketplaceAccountCategory')->findOne([
                    'marketplaceId'=>$val['marketplaceId'],
                    'marketplaceAccountId'=>$val['marketplaceAccountId'],
                    'marketplaceCategoryId'=>$val['marketplaceCategoryId']]);

            } else {
                $marketplaceAccountCateogory  = null;
            }
            $catIds = !is_null($marketplaceAccountCateogory) ? $marketplaceAccountCateogory->getHashKey('md5') : "";

            $row["DT_RowId"] = 'c_'.$productCategory->printId().'__ma_'.$marketplaceAccount->printId();
            $row['marketplace'] = $marketplaceAccount->marketplace->name;
            $row['marketplaceAccount'] = $marketplaceAccount->name;
            $row['productCategory'] = $productCategory->getLocalizedPath();

			if(!$okManage) {
				$html = 'Non si può';
	        } else {
            	$html = '<select class="full-width selectpicker selectize-streachable" 
		                         placeholder="Seleziona la categoria"
		                         data-name="marketplaceCategorySelect"
		                         data-marketplace-account="'.$marketplaceAccount->printId().'"
		                         data-selected="'.$catIds.'"
		                         data-id="' . $row["DT_RowId"] . '" 
		                         tabindex="-1" ></select>';
            }
            $row['marketplaceAccountCategory'] = $html;

            $response['data'][] = $row;
        }
        return json_encode($response);
    }
}