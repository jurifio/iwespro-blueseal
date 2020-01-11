<?php

namespace bamboo\controllers\back\ajax;

use bamboo\domain\entities\CAddressBook;


/**
 * Class CMarketplaceAccountManage
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
class CMarketplaceAccountManage extends AAjaxController
{
	public function get()
	{
	    $marketplaceAcountId = $this->app->router->request()->getRequestData('id');
        $marketplaceAcount = \Monkey::app()->repoFactory->create('MarketplaceAccount')->findOneByStringId($marketplaceAcountId);
        $marketplaceAcount->marketplace;
        return json_encode($marketplaceAcount);
	}

	public function put()
    {
        $shopData = $this->app->router->request()->getRequestData('shop');
        $shopsId = \Monkey::app()->repoFactory->create('Shop')->getAutorizedShopsIdForUser();
    }
    public function post()
    {
            $data  = $this->app->router->request()->getRequestData();
        $marketplaceAccountId=$data['marketplaceAccountId'];
        $marketplaceId=$data['marketplaceId'];
        $nameOld=$data['marketplace_account_name'];
        $range5Cpc=$data['range5Cpc'];
        $productSizeGroup5=$data['productSizeGroup5'];

        $collect=$data['collect'];
        $collectData=json_decode($collect,true);
        $name=$collectData['nameAggregator'];
       $collectUpdate='{"nameAggregator":"'.$collectData['nameAggregator'].'", "lang":"'.$collectData['lang'].'", "slug":"'.$collectData['slug'].'", "filePath":"'.$collectData['filePath'].'", "feedUrl":"'.$collectData['feedUrl'].'",
"activeAutomatic":'.$collectData['activeAutomatic'].', "defaultCpc":'.$collectData['defaultCpc'].', "timeRange":'.$collectData['timeRange'].', "multiplierDefault":'.$collectData['multiplierDefault'].', "priceModifier":'.$collectData['priceModifier'].',
"budgetMonth":'.$collectData['budgetMonth'].' ,"defaultCpcM":'.$collectData['defaultCpcM'].',"defaultCpcF":'.$collectData['defaultCpcF'].',"emailDepublish":"'.$collectData['emailDepublish'].'","emailNotifyOffline":"'.$collectData['emailNotifyOffline'].'",
"productSizeGroupEx1":'.$collectData['productSizeGroupEx1'].',"productSizeGroupEx2":'.$collectData['productSizeGroupEx2'].',"productSizeGroupEx3":'.$collectData['productSizeGroupEx3'].',"productSizeGroupEx4":'.$collectData['productSizeGroupEx4'].',"productSizeGroupEx5":'.$collectData['productSizeGroupEx5'].',"productSizeGroupEx6":'.$collectData['productSizeGroupEx6'].',
"productCategoryIdEx1":'.$collectData['productCategoryIdEx1'].',"productCategoryIdEx2":'.$collectData['productCategoryIdEx2'].',"productCategoryIdEx3":'.$collectData['productCategoryIdEx3'].',"productCategoryIdEx4":'.$collectData['productCategoryIdEx4'].',"productCategoryIdEx5":'.$collectData['productCategoryIdEx5'].',"productCategoryIdEx6":'.$collectData['productCategoryIdEx6'].',
"priceModifierRange1":"'.$collectData['priceModifierRange1'].'","valueexcept1":'.$collectData['valueexcept1'].',"maxCos1":'.$collectData['maxCos1'].',"range1Cpc":'.$collectData['range1Cpc'].',"productSizeGroup1":'.$collectData['productSizeGroup1'].',"productCategoryId1":'.$collectData['productCategoryId1'].',
"priceModifierRange2":"'.$collectData['priceModifierRange2'].'","valueexcept2":'.$collectData['valueexcept2'].',"maxCos2":'.$collectData['maxCos2'].',"range2Cpc":'.$collectData['range2Cpc'].',"productSizeGroup2":'.$collectData['productSizeGroup2'].',"productCategoryId2":'.$collectData['productCategoryId2'].',
"priceModifierRange3":"'.$collectData['priceModifierRange3'].'","valueexcept3":'.$collectData['valueexcept3'].',"maxCos3":'.$collectData['maxCos3'].',"range3Cpc":'.$collectData['range3Cpc'].',"productSizeGroup3":'.$collectData['productSizeGroup3'].',"productCategoryId3":'.$collectData['productCategoryId3'].',
"priceModifierRange4":"'.$collectData['priceModifierRange4'].'","valueexcept4":'.$collectData['valueexcept4'].',"maxCos4":'.$collectData['maxCos4'].',"range4Cpc":'.$collectData['range4Cpc'].',"productSizeGroup4":'.$collectData['productSizeGroup4'].',"productCategoryId4":'.$collectData['productCategoryId4'].',
"priceModifierRange5":"'.$collectData['priceModifierRange5'].'","valueexcept5":'.$collectData['valueexcept5'].',"maxCos5":'.$collectData['maxCos5'].',"range5Cpc":'.$range5Cpc.',"productSizeGroup5":'.$productSizeGroup5.',"productCategoryId5":'.$collectData['productCategoryId5'].'}';

        $marketplaceAccount=\Monkey::app()->repoFactory->create('MarketPlaceAccount')->findOneBy(['id'=>$marketplaceAccountId,'marketplaceId'=>$marketplaceId]);
        $marketplaceConfig = json_encode($marketplaceAccount->config,false);
        if($name!=null) {
            $marketplaceAccount->name = $name;
        }else{
            $marketplaceAccount->name = $nameOld;
        }
        $marketplaceAccount->config=$collectUpdate;
        $marketplaceAccount->update();
        \Monkey::app()->applicationLog('MarketPlaceAccount','Report','Update','Update Marketplace '.$marketplaceAccountId.'-'.$marketplaceId. ' '.$name);
    }
}