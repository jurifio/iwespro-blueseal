<?php

namespace bamboo\controllers\back\ajax;

use bamboo\domain\entities\CAddressBook;


/**
 * Class CMarketplaceAccountInsertManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 11/01/2020
 * @since 1.0
 */
class CMarketplaceAccountInsertManage extends AAjaxController
{

    public function post()
    {
            $marketplaceRepo=\Monkey::app()->repoFactory->create('MarketPlace');
            $marketplaceAccountRepo=\Monkey::app()->repoFactory->create('MarketplaceAccount');
            $data  = $this->app->router->request()->getRequestData();
            $marketplace_account_name=$data['marketplace_account_name'];
            $slug=$data['slug'];
            $nameAdminister=$data['nameAdminister'];
            $emailNotify=$data['emailNotify'];
            $isActive=$data['isActive'];
            $defaultCpcF=$data['defaultCpcF'];
            $defaultCpcFM=$data['defaultCpcFM'];
            $defaultCpcM=$data['defaultCpcM'];
            $defaultCpc=$data['defaultCpc'];
            $budget01=$data['budget01'];
            $budget02=$data['budget02'];
            $budget03=$data['budget03'];
            $budget04=$data['budget04'];
            $budget05=$data['budget05'];
            $budget06=$data['budget06'];
            $budget07=$data['budget07'];
            $budget08=$data['budget08'];
            $budget09=$data['budget09'];
            $budget10=$data['budget10'];
            $budget11=$data['budget11'];
            $budget12=$data['budget12'];
            $typeInsertion=$data['typeInsertion'];
            $marketplaceName=$data['marketplaceName'];
            $productCategoryIdEx1=$data['productCategoryIdEx1'];
            $productCategoryIdEx2=$data['productCategoryIdEx2'];
            $productCategoryIdEx3=$data['productCategoryIdEx3'];
            $productCategoryIdEx4=$data['productCategoryIdEx4'];
            $productCategoryIdEx5=$data['productCategoryIdEx5'];
            $productCategoryIdEx6=$data['productCategoryIdEx6'];
            $productSizeGroupEx1=$data['productSizeGroupEx1'];
            $productSizeGroupEx2=$data['productSizeGroupEx2'];
            $productSizeGroupEx3=$data['productSizeGroupEx3'];
            $productSizeGroupEx4=$data['productSizeGroupEx4'];
            $productSizeGroupEx5=$data['productSizeGroupEx5'];
            $productSizeGroupEx6=$data['productSizeGroupEx6'];
            $priceModifierRange1=$data['priceModifierRange1'];
            $priceModifierRange2=$data['priceModifierRange2'];
            $priceModifierRange3=$data['priceModifierRange3'];
            $priceModifierRange4=$data['priceModifierRange4'];
            $priceModifierRange5=$data['priceModifierRange5'];
            $range1Cpc=$data['range1Cpc'];
            $range2Cpc=$data['range2Cpc'];
            $range3Cpc=$data['range3Cpc'];
            $range4Cpc=$data['range4Cpc'];
            $range5Cpc=$data['range5Cpc'];
            $productSizeGroup1=$data['productSizeGroupId1'];
            $productSizeGroup2=$data['productSizeGroupId2'];
            $productSizeGroup3=$data['productSizeGroupId3'];
            $productSizeGroup4=$data['productSizeGroupId4'];
            $productSizeGroup5=$data['productSizeGroupId5'];
            $productCategoryId1=$data['productCategoryId1'];
            $productCategoryId2=$data['productCategoryId2'];
            $productCategoryId3=$data['productCategoryId3'];
            $productCategoryId4=$data['productCategoryId4'];
            $productCategoryId5=$data['productCategoryId5'];
            if($typeInsertion==1){
                $marketplace=$marketplaceRepo->findOneBy(['name'=>$marketplaceName]);
                if($marketplace==null){
                    $marketplaceInsert=$marketplaceRepo->getEmptyEntity();
                    $marketplaceInsert->name=$marketplaceName;
                    $marketplaceInsert->type='cpc';
                    $marketplaceInsert->insert();
                    $marketplaceFindLastId=$marketplaceRepo->findOneBy(['name'=>$marketplaceName]);
                    $marketplaceId=$marketplaceFindLastId->id;
                }else{
                   return ;
                }


            }else{
                $marketplaceId=$marketplaceName;
            }
            $maxCos1=0.1;
            $maxCos2=0.1;
            $maxCos3=0.1;
            $maxCos4=0.1;
            $maxCos5=0.1;
            $valueexcept1=0.1;
            $valueexcept2=0.1;
            $valueexcept3=0.1;
            $valueexcept4=0.1;
            $valueexcept5=0.1;
            $filePath='/export/'.ucfirst($slug).'BetterFeedTemp'.$lang.'.xml';
            $feedUrl='/services/feed/'.$lang.'/'.$slug;
            $timeRange=7;
            $multiplierDefault=0.1;
            $priceModifier=0;


       $collectUpdate='{"nameAggregator":"'.$marketplace_account_name.'",
                        "lang":"'.$lang.'", 
                        "slug":"'.$slug.'", 
                        "filePath":"'.$filePath.'", 
                        "feedUrl":"'.$feedUrl.'",
                        "activeAutomatic":'.$isActive.',
                        "defaultCpc":'.$defaultCpc.',
                        "defaultCpcM":'.$defaultCpcM.',
                        "defaultCpcF":'.$defaultCpcF.',
                        "defaultCpcFM":'.$defaultCpcFM.',
                        "timeRange":'.$timeRange.', 
                        "multiplierDefault":'.$multiplierDefault.', 
                        "priceModifier":'.$priceModifier.',
                        "budget01":'.$budget01.' ,
                        "budget02":'.$budget02.' ,
                        "budget03":'.$budget03.' ,
                        "budget04":'.$budget04.' ,
                        "budget05":'.$budget05.' ,
                        "budget06":'.$budget06.' ,
                        "budget07":'.$budget07.' ,
                        "budget08":'.$budget08.' ,
                        "budget09":'.$budget09.' ,
                        "budget10":'.$budget10.' ,
                        "budget11":'.$budget11.' ,
                        "budget12":'.$budget12.' ,
                        "nameAdminister":"'.$nameAdminister.'",
                        "emailNotify":"'.$emailNotify.'",
                        "productSizeGroupEx1":'.$productSizeGroupEx1.',
                        "productSizeGroupEx2":'.$productSizeGroupEx2.',
                        "productSizeGroupEx3":'.$productSizeGroupEx3.',
                        "productSizeGroupEx4":'.$productSizeGroupEx4.',
                        "productSizeGroupEx5":'.$productSizeGroupEx5.',
                        "productSizeGroupEx6":'.$productSizeGroupEx6.',
                        "productCategoryIdEx1":'.$productCategoryIdEx1.',
                        "productCategoryIdEx2":'.$productCategoryIdEx2 .',
                        "productCategoryIdEx3":'.$productCategoryIdEx3 .',
                        "productCategoryIdEx4":'.$productCategoryIdEx4 .',
                        "productCategoryIdEx5":'.$productCategoryIdEx5 .',
                        "productCategoryIdEx6":'.$productCategoryIdEx6 .',
                        "priceModifierRange1":"'.$priceModifierRange1 .'",
                        "valueexcept1":'.$valueexcept1 .',
                        "maxCos1":'.$maxCos1 .',
                        "range1Cpc":'.$range1Cpc .',
                        "productSizeGroup1":'.$productSizeGroup1 .',
                        "productCategoryId1":'.$productCategoryId1 .',
                        "priceModifierRange2":"'.$priceModifierRange2 .'",
                        "valueexcept2":'.$valueexcept2 .',
                        "maxCos2":'.$maxCos2 .',
                        "range2Cpc":'.$range2Cpc .',
                        "productSizeGroup2":'.$productSizeGroup2 .',
                        "productCategoryId2":'.$productCategoryId2 .',
                        "priceModifierRange3":"'.$priceModifierRange3 .'",
                        "valueexcept3":'.$valueexcept3 .',
                        "maxCos3":'.$maxCos3 .',
                        "range3Cpc":'.$range3Cpc .',
                        "productSizeGroup3":'.$productSizeGroup3 .',
                        "productCategoryId3":'.$productCategoryId3 .',
                        "priceModifierRange4":"'.$priceModifierRange4 .'",
                        "valueexcept4":'.$valueexcept4 .',
                        "maxCos4":'.$maxCos4 .',
                        "range4Cpc":'.$range4Cpc .',
                        "productSizeGroup4":'.$productSizeGroup4 .',
                        "productCategoryId4":'.$productCategoryId4 .',
                        "priceModifierRange5":"'.$priceModifierRange5 .'",
                        "valueexcept5":'.$valueexcept5 .',
                        "maxCos5":'.$maxCos5 .',
                        "range5Cpc":'.$range5Cpc.',
                        "productSizeGroup5":'.$productSizeGroup5.',
                        "productCategoryId5":'.$productCategoryId5 .'}';

        $marketplaceAccount=\Monkey::app()->repoFactory->create('MarketPlaceAccount')->getEmptyEntity();
        $marketplaceAccount->marketplaceId=$marketplaceId;
        $marketplaceAccount->name = $marketplace_account_name;
        $marketplaceAccount->config=$collectUpdate;
        $marketplaceAccount->insert();
        $markeplcaAccountIdFind=$marketplaceAccountRepo->findOneBy(['name'=>$marketplace_account_name]);
        $marketplaceAccountId=$markeplcaAccountIdFind->id;
        \Monkey::app()->applicationLog('MarketPlaceAccount','Report','Insert','Insert Marketplace Account '.$marketplaceAccountId.'-'.$marketplaceId. ' '.$name);
    }
}