<?php
namespace bamboo\blueseal\controllers\ajax;

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
	    $sample = $this->app->repoFactory->create('MarketplaceAccountCategory')->getEmptyEntity();

        $datatable = new CDataTables('vBluesealMarketplaceCategoryInverted',['marketplaceId','marketplaceAccountId','productCategoryId'],$_GET);
		$datatable->addCondition('isRelevant',[1]);

        $orribilità = $this->app->dbAdapter->query($datatable->getQuery(false,true),$datatable->getParams())->fetchAll();
        $count = $this->app->dbAdapter->query($datatable->getQuery(true,true),$datatable->getParams())->fetch();
        $totalCount = $this->app->dbAdapter->query($datatable->getQuery('full',true),$datatable->getParams())->fetch();

        $okManage = $this->app->getUser()->hasPermission('/admin/product/edit');

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        foreach($orribilità as $val) {
            $row = [];
            /** @var CProductCategory $productCategory */
            $productCategory = \Monkey::app()->repoFactory->create('ProductCategory')->findOne([$val['productCategoryId']]);
            $marketplaceAccount = \Monkey::app()->repoFactory->create('ProductCategory')->findOne(['id'=>$val['marketplaceAccountId'],'marketplaceId'=>$val['marketplaceId']]);
            $marketplaceAccountCateogory = \Monkey::app()->repoFactory->create('MarketplaceAccountCategory')->findOne($marketplaceAccount->getIds()+[$val['marketplaceCategoryId']]);
            $row["DT_RowId"] = 'c-'.$productCategory->printId().'__ma-'.$marketplaceAccount->printId();
            $row['marketplace'] = $marketplaceAccount->marketplace->name;
            $row['marketplaceAccount'] = $marketplaceAccount->name;
            $row['productCategory'] = $productCategory->getLocalizedPath();

			if(!$okManage) {
				$html = 'Non si può';
	        } else {
                $catIds = !is_null($marketplaceAccountCateogory) ? $marketplaceAccountCateogory->getHashKey() : "";
	        	$html = '<select class="full-width selectpicker selectize-streachable" 
		                         placeholder="Seleziona la categoria"
		                         data-name="categorySelect"
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