<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CMarketplaceAccountHasProduct;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CCAmpaign;
use bamboo\domain\entities\CCampaignVisit;
use bamboo\domain\entities\CCampaignVisitHasOrder;
use bamboo\domain\entities\CCampaignVisitHasProduct;
use bamboo\domain\repositories\CCampaignRepo;
use bamboo\domain\repositories\CMarketplaceAccountHasProductRepo;

/**
 * Class CCampaignVisitListAjaxController
 * @package bamboo\controllers\back\ajax
 */
class CCampaignVisitListAjaxController extends AAjaxController
{
    public function get()
    {
        $campaignId = isset($this->data['campaignId'])?$this->data['campaignId']:'';
        if ($campaignId != '') {
            $sqlCampaignId = '  AND cvhp.campaignId='.$campaignId;
        }else{
            $sqlCampaignId= ' ';
        }


        $sample = \Monkey::app()->repoFactory->create('CampaignVisitHasProduct')->getEmptyEntity();




        $query = "SELECT c.id,
                         cvhp.remoteShopId as remoteShopId,
                        c.name as campaignName,
                        c.code as campaignCode,
                        cv.campaignId as campaignId,
                        cv.timestamp as campaignVisit,
						cvhp.campaignVisitId as CampaignVisitId,
                        cvhp.productId as productId,
                        cvhp.productVariantId as productVariantId,
                        concat(cvhp.productId, '-',cvhp.productVariantId) as codeProduct,
                        c.defaultCpc as defaultCpc,
                        s.name as shopName,
                        round(sum(cv.cost),2) AS cost,
                        count(DISTINCT cv.id) AS visits,
                        count(DISTINCT cvho.orderId) AS orderCount,
                        ifnull(sum(DISTINCT ol.netPrice),0) AS orderValue,
                        mahp.priceModifier as priceModifier
                FROM CampaignVisitHasProduct cvhp
                    JOIN Campaign c  ON c.id = cvhp.campaignId
                  JOIN CampaignVisit cv ON (cvhp.campaignVisitId, cvhp.campaignId) =  (cv.id, cv.campaignId)  
                
                      left join Shop s on cvhp.remoteShopId =s.id
                  JOIN MarketplaceAccountHasProduct mahp on (cvhp.productId=mahp.productId and cvhp.productVariantId=mahp.productVariantId)
                  
                  LEFT JOIN 
                    CampaignVisitHasOrder cvho JOIN OrderLine ol ON cvho.orderId = ol.orderId
                   ON (cv.id, cv.campaignId) = (cvho.campaignVisitId, cvho.campaignId) AND 
                    (cvhp.productId,cvhp.productVariantId) = (ol.productId,ol.productVariantId)
                WHERE cv.timestamp > (NOW() - INTERVAL 1 WEEK) ". $sqlCampaignId ."
                GROUP BY cvhp.productId,cvhp.productVariantId
                HAVING cost > 0
                ORDER BY c.id ASC";


        $timeFrom = \DateTime::createFromFormat('Y-m-d',$this->app->router->request()->getRequestData('startDate'));
        $timeTo = \DateTime::createFromFormat('Y-m-d',$this->app->router->request()->getRequestData('endDate'));
        $timeFrom = $timeFrom ? $timeFrom->format('Y-m-d') : null;
        $timeTo = $timeTo ? $timeTo->format('Y-m-d') : null;
        $queryParameters = [$timeFrom,$timeTo];
        /** @var CMarketplaceAccountHasProductRepo $marketplaceAccountHasProductRepo */
        $marketplaceAccountHasProductRepo = \Monkey::app()->repoFactory->create('MarketplaceAccountHasProduct');
        /** @var CRepo $campaignVisitHasProductRepo */
        $campaignVisitHasProductRepo = \Monkey::app()->repoFactory->create('CampaignVisitHasProduct');
        /** @var CRepo $campaignVisitHasOrderRepo */
        $campaignVisitHasOrderRepo = \Monkey::app()->repoFactory->create('CampaignVisitHasOrder');
        /** @var CRepo $campaignVisitRepo */
        $campaignVisitRepo = \Monkey::app()->repoFactory->create('CampaignVisit');


        $datatable = new CDataTables($query,$sample->getPrimaryKeys(),$_GET,true);
        $productRepo = \Monkey::app()->repoFactory->create('Product');
        /** @var CCampaignRepo $campaingRepo */
        $campaingRepo = \Monkey::app()->repoFactory->create('Campaign');

        $campaigns = $this->app->dbAdapter->query($datatable->getQuery(false,true),$datatable->getParams())->fetchAll();
        // $campaigns = $this->app->dbAdapter->query($datatable->getQuery(false,true),array_merge($datatable->getParams()))->fetchAll();
        $count = $sample->em()->findCountBySql($datatable->getQuery(true),$datatable->getParams());
        $totalCount = $sample->em()->findCountBySql($datatable->getQuery('full'),($datatable->getParams()));
        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        foreach ($campaigns as $campaignData) {

            // cerco il prodotto
            /** @var CProduct $product */
            $product = $productRepo->findOneBy(["id" => $campaignData['productId'],"productVariantId" => $campaignData['productVariantId']]);
            //cerco la campagna che è presente nei dati
            /** @var CCampaign $campaign */
            $productSizeGroupId = $product->productSizeGroupId;

            $campaign = $campaingRepo->findOneBy(["id" => $campaignData['campaignId']]);
            // verifico se la campagna è lincata al marketplace
            $iniSizes = $product->productSku->count();
            $actualSizes = 0;
            foreach ($product->productSku as $sku) {
                if ($sku->stockQty > 0) $actualSizes++;
            }
            $checkIfProductSizeGroupExId1 = isset($campaign->marketplaceAccount->getConfig()['productSizeGroupEx1']) ? $campaign->marketplaceAccount->getConfig()['productSizeGroupEx1'] : 0;
            $checkIfProductSizeGroupExId2 = isset($campaign->marketplaceAccount->getConfig()['productSizeGroupEx2']) ? $campaign->marketplaceAccount->getConfig()['productSizeGroupEx2'] : 0;
            $checkIfProductSizeGroupExId3 = isset($campaign->marketplaceAccount->getConfig()['productSizeGroupEx3']) ? $campaign->marketplaceAccount->getConfig()['productSizeGroupEx3'] : 0;
            $checkIfProductSizeGroupExId4 = isset($campaign->marketplaceAccount->getConfig()['productSizeGroupEx4']) ? $campaign->marketplaceAccount->getConfig()['productSizeGroupEx4'] : 0;
            $checkIfProductSizeGroupExId5 = isset($campaign->marketplaceAccount->getConfig()['productSizeGroupEx5']) ? $campaign->marketplaceAccount->getConfig()['productSizeGroupEx5'] : 0;
            if ($checkIfProductSizeGroupId1 == $productSizeGroupId) {
                continue;
            } elseif ($checkIfProductSizeGroupId2 == $productSizeGroupId) {
                continue;
            } elseif ($checkIfProductSizeGroupId3 == $productSizeGroupId) {
                continue;
            } elseif ($checkIfProductSizeGroupId4 == $productSizeGroupId) {
               continue;
            } elseif ($checkIfProductSizeGroupId5 == $productSizeGroupId) {
              continue;
            }


            $sizeFill = $actualSizes / $iniSizes;

            if ($sizeFill === 0) $nCos = 'NaN';
            else $nCos = round((
                    $campaignData['cost'] /
                    (
                        ($campaignData['orderCount'] == 0 ? 0.1 : $campaignData['orderCount']) * $product->getDisplayActivePrice()
                    )
                    * $sizeFill
                ) * 100,2);
            // costo cpc fratto il conteggio degli ordini con quel prodotto moltiplicato il prezzo attivo  moltiplicato la giacenza media moltiplicato per 100
            if ($campaignData['orderCount'] == 0) $cos = 'NaN';
            else $cos = round($campaignData['cost'] / $campaignData['orderValue'] * 100,2);
            /** costo campagna  / somma totale degli ordini per cento   */

            //definizione del massimo costo per giorno in base alla query
         //   $maxCos = $campaign->marketplaceAccount->getConfig()['maxCos'] ?? 7;
            $priceModifierRange1 = explode('-',$campaign->marketplaceAccount->getConfig()['priceModifierRange1']);
            $priceModifierRange2 = explode('-',$campaign->marketplaceAccount->getConfig()['priceModifierRange2']);
            $priceModifierRange3 = explode('-',$campaign->marketplaceAccount->getConfig()['priceModifierRange3']);
            $priceModifierRange4 = explode('-',$campaign->marketplaceAccount->getConfig()['priceModifierRange4']);
            $priceModifierRange5 = explode('-',$campaign->marketplaceAccount->getConfig()['priceModifierRange5']);
            switch (true) {
                case ($product->getDisplayActivePrice() >= $priceModifierRange1[0] && $product->getDisplayActivePrice() <= $priceModifierRange1[1]):
                    $maxCos = $campaign->marketplaceAccount->getConfig()['maxCos1'] ?? 7;
                    $checkIfProductSizeGroupId1 = isset($campaign->marketplaceAccount->getConfig()['productSizeGroup1']) ? $campaign->marketplaceAccount->getConfig()['productSizeGroup1'] : 0;
                    if( $checkIfProductSizeGroupId1==$productSizeGroupId){
                        $multiplierIs = $campaign->marketplaceAccount->getConfig()['valueexcept1'];
                    } else {
                        $multiplierIs = isset($campaign->marketplaceAccount->getConfig()['multiplierDefault']) ? $campaign->marketplaceAccount->getConfig()['multiplierDefault'] : 0.1;
                    }
                    break;
                case ($product->getDisplayActivePrice() >= $priceModifierRange2[0] && $product->getDisplayActivePrice() <= $priceModifierRange2[1]):
                    $maxCos = $campaign->marketplaceAccount->getConfig()['maxCos2'] ?? 7;
                    $checkIfProductSizeGroupId2 = isset($campaign->marketplaceAccount->getConfig()['productSizeGroup2']) ? $campaign->marketplaceAccount->getConfig()['productSizeGroup2'] : 0;
                    if( $checkIfProductSizeGroupId2==$productSizeGroupId){
                        $multiplierIs = $campaign->marketplaceAccount->getConfig()['valueexcept2'];
                    } else {
                        $multiplierIs = isset($campaign->marketplaceAccount->getConfig()['multiplierDefault']) ? $campaign->marketplaceAccount->getConfig()['multiplierDefault'] : 0.1;
                    }
                    break;
                case ($product->getDisplayActivePrice() >= $priceModifierRange3[0] && $product->getDisplayActivePrice() <= $priceModifierRange3[1]):
                    $maxCos = $campaign->marketplaceAccount->getConfig()['maxCos3'] ?? 7;
                    $checkIfProductSizeGroupId3 = isset($campaign->marketplaceAccount->getConfig()['productSizeGroup3']) ? $campaign->marketplaceAccount->getConfig()['productSizeGroup3'] : 0;
                    if( $checkIfProductSizeGroupId3==$productSizeGroupId){
                        $multiplierIs = $campaign->marketplaceAccount->getConfig()['valueexcept3'];
                    } else {
                        $multiplierIs = isset($campaign->marketplaceAccount->getConfig()['multiplierDefault']) ? $campaign->marketplaceAccount->getConfig()['multiplierDefault'] : 0.1;
                    }
                    break;
                case ($product->getDisplayActivePrice() >= $priceModifierRange4[0] && $product->getDisplayActivePrice() <= $priceModifierRange4[1]):
                    $maxCos = $campaign->marketplaceAccount->getConfig()['maxCos4'] ?? 7;
                    $checkIfProductSizeGroupId4 = isset($campaign->marketplaceAccount->getConfig()['productSizeGroup4']) ? $campaign->marketplaceAccount->getConfig()['productSizeGroup4'] : 0;
                    if( $checkIfProductSizeGroupId4==$productSizeGroupId){
                        $multiplierIs = $campaign->marketplaceAccount->getConfig()['valueexcept4'];
                    } else {
                        $multiplierIs = isset($campaign->marketplaceAccount->getConfig()['multiplierDefault']) ? $campaign->marketplaceAccount->getConfig()['multiplierDefault'] : 0.1;
                    }
                    break;
                case ($product->getDisplayActivePrice() >= $priceModifierRange5[0] && $product->getDisplayActivePrice() <= $priceModifierRange5[1]):
                    $maxCos = $campaign->marketplaceAccount->getConfig()['maxCos5'] ?? 7;
                    $checkIfProductSizeGroupId5 = isset($campaign->marketplaceAccount->getConfig()['productSizeGroup5']) ? $campaign->marketplaceAccount->getConfig()['productSizeGroup5'] : 0;
                    if( $checkIfProductSizeGroupId5==$productSizeGroupId){
                        $multiplierIs = $campaign->marketplaceAccount->getConfig()['valueexcept5'];
                    } else {
                        $multiplierIs = isset($campaign->marketplaceAccount->getConfig()['multiplierDefault']) ? $campaign->marketplaceAccount->getConfig()['multiplierDefault'] : 0.1;
                    }
                    break;

            }
            if ($nCos === 'NaN' || $nCos > $maxCos) {
                $messageDelete = "Deleting product from Marketplace, cos: $nCos, over maxCos: " . $maxCos;

            } else {
                $messageDelete = '';
            }


            $row['id'] = $campaignData['id'];
            $row['retailPrice']=number_format($product->getDisplayActivePrice(),2,',',' ');
            $row['pondRetailPrice']=number_format(($product->getDisplayActivePrice()*$multiplierIs),2,',',' ');
            $row['campaignCode'] = $campaignData['campaignCode'];
            $row['campaignName'] = $campaignData['campaignName'];
            $row['codeProduct'] = $campaignData['codeProduct'];
            $row['campaignVisit'] = $campaignData['campaignVisit'];
            $row['defaultCpc'] = number_format($campaignData['defaultCpc'],2,',',' ');
            $row['shopName'] = $campaignData['shopName'];
            $row['visits'] = $campaignData['visits'];
            $row['cost'] = number_format($campaignData['cost'],2,',',' ');;
            $row['orderCount'] = $campaignData['orderCount'];
            $row['orderValue'] = $campaignData['orderValue'];
            $row['priceModifier'] = $campaignData['priceModifier'];
            $row['cos'] = $cos;
            $row['maxCos'] = $maxCos;
            $row['sizeFill'] = number_format( $sizeFill,2,',',' ');

            $row['messageDelete'] = $messageDelete;
            $row['multiplierIs'] = $multiplierIs;

            $response['data'][] = $row;
        }


        return json_encode($response);
    }
}