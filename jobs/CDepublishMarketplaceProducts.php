<?php

namespace bamboo\blueseal\jobs;

use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CCampaign;
use bamboo\domain\entities\CMarketplaceAccountHasProduct;
use bamboo\domain\entities\CProduct;
use bamboo\domain\repositories\CCampaignRepo;
use bamboo\domain\repositories\CMarketplaceAccountHasProductRepo;
use bamboo\domain\repositories\CProductRepo;

/**
 * Class CDepublishMarketplaceProducts
 * @package bamboo\blueseal\jobs
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
class CDepublishMarketplaceProducts extends ACronJob
{
    /**
     * @param null $args
     */
    public function run($args = null)
    {
        $config = json_decode($args);
        $this->report('run', 'Starting with config' . $args);
        $sql = "SELECT c.id,
                        cvhp.productId,
                        cvhp.productVariantId,
                        round(sum(cv.cost),2) AS cost,
                        count(DISTINCT cv.id) AS visits,
                        count(DISTINCT cvho.orderId) AS orderCount,
                        ifnull(sum(DISTINCT ol.netPrice),0) AS orderValue
                FROM Campaign c
                  JOIN CampaignVisit cv ON c.id = cv.campaignId
                  JOIN CampaignVisitHasProduct cvhp ON (cv.id, cv.campaignId) = (cvhp.campaignVisitId, cvhp.campaignId)
                  LEFT JOIN (
                    CampaignVisitHasOrder cvho JOIN OrderLine ol ON cvho.orderId = ol.orderId
                  ) ON (cv.id, cv.campaignId) = (cvho.campaignVisitId, cvho.campaignId) AND 
                    (cvhp.productId,cvhp.productVariantId) = (ol.productId,ol.productVariantId)
                WHERE cv.timestamp > (NOW() - INTERVAL 1 WEEK)
                GROUP BY c.id,cvhp.productId,cvhp.productVariantId
                HAVING cost > 0
                ORDER BY c.id ASC";

        $res = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();
        /** @var CProductRepo $productRepo */
        $productRepo = \Monkey::app()->repoFactory->create('Product');
        /** @var CCampaignRepo $campaingRepo */
        $campaingRepo = \Monkey::app()->repoFactory->create('Campaign');

        /** @var CMarketplaceAccountHasProductRepo $marketplaceAccountHasProductRepo */
        $marketplaceAccountHasProductRepo = \Monkey::app()->repoFactory->create('MarketplaceAccountHasProduct');
        $productCategoryTranslationRepo=\Monkey::app()->repoFactory->create('ProductCategoryTranslation');
        $productHasProductCategoryRepo=\Monkey::app()->repoFactory->create('ProductHasProductCategory');
        //creo il report
        $reportArray = [];
        $this->report('run', 'Starting Cycle for ' . count($res));
        // per ogni risultato nella query
        foreach ($res as $one) {
            // cerco il prodotto
            /** @var CProduct $product */
            $product = $productRepo->findOneBy(["id" => $one['productId'], "productVariantId" => $one['productVariantId']]);
            //cerco la campagna che è presente nei dati
            /** @var CCampaign $campaign */
            $productSizeGroupId=$product->productSizeGroupId;
            $campaign = $campaingRepo->findOneBy(["id" => $one['id']]);
            // verifico se la campagna è lincata al marketplace
            if (!$campaign->marketplaceAccount) {
                $this->warning('Cycle', 'Campaign not linked with marketplace', $one);
            }
            $productHasProductCategory=$productHasProductCategoryRepo->findOneBy(["productId" => $campaignData['productId'],"productVariantId" => $campaignData['productVariantId']]);
            $productCategoryId=$productHasProductCategory->productCategoryId;
            $sqlCategory='SELECT t0.slug as  node,t0.id as  id, pct.name as parentCategory
      ,(SELECT GROUP_CONCAT(t2.slug)
                    FROM ProductCategory t2
                    WHERE t2.lft<t0.lft AND t2.rght>t0.rght
                    ORDER BY t2.lft) ancestors
FROM ProductCategory  t0 JOIN ProductCategoryTranslation pct ON t0.id=pct.productCategoryId    WHERE depth=3 AND t0.id='.$productCategoryId.'
GROUP BY t0.slug';
            $res_category = \Monkey::app()->dbAdapter->query($sqlCategory, [])->fetchAll();
            if($res_category!=null) {
                foreach ($res_category as $category) {
                    $parentCategoryName = $category['parentCategory'];
                }
                $productCategoryTranslation = $productCategoryTranslationRepo->findOneBy(['name' => $parentCategoryName]);
                $parentCategory = $productCategoryTranslation->id;
            }else{
                $parentCategory=0;
            }
            $iniSizes = $product->productSku->count();
            $actualSizes = 0;
            foreach ($product->productSku as $sku) if ($sku->stockQty > 0) $actualSizes++;

            $this->debug('Cycle Res', 'Doing Math',
                [
                    'iniSizes' => $iniSizes,
                    'actualSizes' => $actualSizes,
                    'cost' => $one['cost'],
                    'orderCount' => $one['orderCount'],
                    'productPrice' => $product->getDisplayActivePrice()
                ]);
            $checkIfProductSizeGroupExId1 = isset($campaign->marketplaceAccount->getConfig()['productSizeGroupEx1']) ? $campaign->marketplaceAccount->getConfig()['productSizeGroupEx1'] : 0;
            $checkIfProductSizeGroupExId2 = isset($campaign->marketplaceAccount->getConfig()['productSizeGroupEx2']) ? $campaign->marketplaceAccount->getConfig()['productSizeGroupEx2'] : 0;
            $checkIfProductSizeGroupExId3 = isset($campaign->marketplaceAccount->getConfig()['productSizeGroupEx3']) ? $campaign->marketplaceAccount->getConfig()['productSizeGroupEx3'] : 0;
            $checkIfProductSizeGroupExId4 = isset($campaign->marketplaceAccount->getConfig()['productSizeGroupEx4']) ? $campaign->marketplaceAccount->getConfig()['productSizeGroupEx4'] : 0;
            $checkIfProductSizeGroupExId5 = isset($campaign->marketplaceAccount->getConfig()['productSizeGroupEx5']) ? $campaign->marketplaceAccount->getConfig()['productSizeGroupEx5'] : 0;
            $checkIfProductSizeGroupExId6 = isset($campaign->marketplaceAccount->getConfig()['productSizeGroupEx6']) ? $campaign->marketplaceAccount->getConfig()['productSizeGroupEx6'] : 0;
            if ($checkIfProductSizeGroupExId1 == $productSizeGroupId) {
                continue;
            } elseif ($checkIfProductSizeGroupExId2 == $productSizeGroupId) {
                continue;
            } elseif ($checkIfProductSizeGroupExId3 == $productSizeGroupId) {
                continue;
            } elseif ($checkIfProductSizeGroupExId4 == $productSizeGroupId) {
                continue;
            } elseif ($checkIfProductSizeGroupExId5 == $productSizeGroupId) {
                continue;
            } elseif ($checkIfProductSizeGroupExId6 == $productSizeGroupId) {
                continue;
            }
            $checkIfProductCategoryIdEx1 = isset($campaign->marketplaceAccount->getConfig()['productCategoryIdEx1']) ? $campaign->marketplaceAccount->getConfig()['productCategoryIdEx1'] : 0;
            $checkIfProductCategoryIdEx2 = isset($campaign->marketplaceAccount->getConfig()['productCategoryIdEx2']) ? $campaign->marketplaceAccount->getConfig()['productCategoryIdEx2'] : 0;
            $checkIfProductCategoryIdEx3 = isset($campaign->marketplaceAccount->getConfig()['productCategoryIdEx3']) ? $campaign->marketplaceAccount->getConfig()['productCategoryIdEx3'] : 0;
            $checkIfProductCategoryIdEx4 = isset($campaign->marketplaceAccount->getConfig()['productCategoryIdEx4']) ? $campaign->marketplaceAccount->getConfig()['productCategoryIdEx4'] : 0;
            $checkIfProductCategoryIdEx5 = isset($campaign->marketplaceAccount->getConfig()['productCategoryIdEx5']) ? $campaign->marketplaceAccount->getConfig()['productCategoryIdEx5'] : 0;
            $checkIfProductCategoryIdEx6 = isset($campaign->marketplaceAccount->getConfig()['productCategoryIdEx6']) ? $campaign->marketplaceAccount->getConfig()['productCategoryIdEx6'] : 0;
            if ($checkIfProductCategoryIdEx1 == $parentCategory) {
                continue;
            } elseif ($checkIfProductCategoryIdEx2 == $parentCategory) {
                continue;
            } elseif ($checkIfProductCategoryIdEx3 == $parentCategory) {
                continue;
            } elseif ($checkIfProductCategoryIdEx4 == $parentCategory) {
                continue;
            } elseif ($checkIfProductCategoryIdEx5 == $parentCategory) {
                continue;
            } elseif ($checkIfProductCategoryIdEx6 == $parentCategory) {
                continue;
            }

            $sizeFill = $actualSizes / $iniSizes;

            if ($sizeFill === 0) $nCos = 'NaN';
            else $nCos = round((
                    $one['cost'] /
                    (
                        ($one['orderCount'] == 0 ? 0.1 : $one['orderCount']) * $product->getDisplayActivePrice()
                    )
                    * $sizeFill
                ) * 100,2);
        // costo cpc fratto il conteggio degli ordini con quel prodotto moltiplicato il prezzo attivo  moltiplicato la giacenza media moltiplicato per 100
            if ($one['orderCount'] == 0) $cos = 'NaN';
            else $cos = round($one['cost'] / $one['orderValue'] * 100,2);
            /** costo campagna  / somma totale degli ordini per cento   */
            $this->debug('Cycle Res', 'Math Done', [
                'cos' => $cos,
                'nCos' => $nCos
            ]);
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
                    $checkIfProductCategoryId1 = isset($campaign->marketplaceAccount->getConfig()['productCategoryId1']) ? $campaign->marketplaceAccount->getConfig()['productCategoryId1'] : 0;
                    if( $checkIfProductSizeGroupId1==$productSizeGroupId){
                        $multiplierIs = $campaign->marketplaceAccount->getConfig()['valueexcept1'];
                    } elseif ($checkIfProductCategoryId1 == $parentCategory) {
                        $multiplierIs = $campaign->marketplaceAccount->getConfig()['valueexcept1'];
                    }else{
                        $multiplierIs = isset($campaign->marketplaceAccount->getConfig()['multiplierDefault']) ? $campaign->marketplaceAccount->getConfig()['multiplierDefault'] : 0.1;
                    }
                    break;
                case ($product->getDisplayActivePrice() >= $priceModifierRange2[0] && $product->getDisplayActivePrice() <= $priceModifierRange2[1]):
                    $maxCos = $campaign->marketplaceAccount->getConfig()['maxCos2'] ?? 7;
                    $checkIfProductSizeGroupId2 = isset($campaign->marketplaceAccount->getConfig()['productSizeGroup2']) ? $campaign->marketplaceAccount->getConfig()['productSizeGroup2'] : 0;
                    $checkIfProductCategoryId2 = isset($campaign->marketplaceAccount->getConfig()['productCategoryId2']) ? $campaign->marketplaceAccount->getConfig()['productCategoryId2'] : 0;
                    if( $checkIfProductSizeGroupId2==$productSizeGroupId){
                        $multiplierIs = $campaign->marketplaceAccount->getConfig()['valueexcept2'];
                    } elseif ($checkIfProductCategoryId2 == $parentCategory) {
                        $multiplierIs = $campaign->marketplaceAccount->getConfig()['valueexcept2'];
                    }else{
                        $multiplierIs = isset($campaign->marketplaceAccount->getConfig()['multiplierDefault']) ? $campaign->marketplaceAccount->getConfig()['multiplierDefault'] : 0.1;
                    }
                    break;
                case ($product->getDisplayActivePrice() >= $priceModifierRange3[0] && $product->getDisplayActivePrice() <= $priceModifierRange3[1]):
                    $maxCos = $campaign->marketplaceAccount->getConfig()['maxCos3'] ?? 7;
                    $checkIfProductSizeGroupId3 = isset($campaign->marketplaceAccount->getConfig()['productSizeGroup3']) ? $campaign->marketplaceAccount->getConfig()['productSizeGroup3'] : 0;
                    $checkIfProductCategoryId3 = isset($campaign->marketplaceAccount->getConfig()['productCategoryId3']) ? $campaign->marketplaceAccount->getConfig()['productCategoryId3'] : 0;
                    if( $checkIfProductSizeGroupId3==$productSizeGroupId){
                        $multiplierIs = $campaign->marketplaceAccount->getConfig()['valueexcept3'];
                    } elseif ($checkIfProductCategoryId3 == $parentCategory) {
                        $multiplierIs = $campaign->marketplaceAccount->getConfig()['valueexcept3'];
                    }else{
                        $multiplierIs = isset($campaign->marketplaceAccount->getConfig()['multiplierDefault']) ? $campaign->marketplaceAccount->getConfig()['multiplierDefault'] : 0.1;
                    }
                    break;
                case ($product->getDisplayActivePrice() >= $priceModifierRange4[0] && $product->getDisplayActivePrice() <= $priceModifierRange4[1]):
                    $maxCos = $campaign->marketplaceAccount->getConfig()['maxCos4'] ?? 7;
                    $checkIfProductSizeGroupId4 = isset($campaign->marketplaceAccount->getConfig()['productSizeGroup4']) ? $campaign->marketplaceAccount->getConfig()['productSizeGroup4'] : 0;
                    $checkIfProductCategoryId4 = isset($campaign->marketplaceAccount->getConfig()['productCategoryId4']) ? $campaign->marketplaceAccount->getConfig()['productCategoryId4'] : 0;
                    if( $checkIfProductSizeGroupId4==$productSizeGroupId){
                        $multiplierIs = $campaign->marketplaceAccount->getConfig()['valueexcept4'];
                    } elseif ($checkIfProductCategoryId4 == $parentCategory) {
                        $multiplierIs = $campaign->marketplaceAccount->getConfig()['valueexcept4'];
                    }else{
                        $multiplierIs = isset($campaign->marketplaceAccount->getConfig()['multiplierDefault']) ? $campaign->marketplaceAccount->getConfig()['multiplierDefault'] : 0.1;
                    }
                    break;
                case ($product->getDisplayActivePrice() >= $priceModifierRange5[0] && $product->getDisplayActivePrice() <= $priceModifierRange5[1]):
                    $maxCos = $campaign->marketplaceAccount->getConfig()['maxCos5'] ?? 7;
                    $checkIfProductSizeGroupId5 = isset($campaign->marketplaceAccount->getConfig()['productSizeGroup5']) ? $campaign->marketplaceAccount->getConfig()['productSizeGroup5'] : 0;
                    $checkIfProductCategoryId5 = isset($campaign->marketplaceAccount->getConfig()['productCategoryId5']) ? $campaign->marketplaceAccount->getConfig()['productCategoryId5'] : 0;
                    if( $checkIfProductSizeGroupId5==$productSizeGroupId){
                        $multiplierIs = $campaign->marketplaceAccount->getConfig()['valueexcept5'];
                    } elseif ($checkIfProductCategoryId5 == $parentCategory) {
                        $multiplierIs = $campaign->marketplaceAccount->getConfig()['valueexcept5'];
                    }else{
                        $multiplierIs = isset($campaign->marketplaceAccount->getConfig()['multiplierDefault']) ? $campaign->marketplaceAccount->getConfig()['multiplierDefault'] : 0.1;
                    }
                    break;

            }
            $this->report('Cycle', "Define  cos: $nCos, and maxCos: " . $maxCos, ['product' => $product, 'campaing' => $campaign]);
            if ($nCos === 'NaN' || $nCos > $maxCos) {
                $this->report('Cycle', "Deleting product from Marketplace, cos: $nCos, over maxCos: " . $maxCos, ['product' => $product, 'campaing' => $campaign]);
                /** @var CMarketplaceAccountHasProduct $marketplaceAccountHasProduct */
                $marketplaceAccountHasProduct = $marketplaceAccountHasProductRepo->getEmptyEntity();
                $marketplaceAccountHasProduct->productId = $product->id;
                $marketplaceAccountHasProduct->productVariantId = $product->productVariantId;
                $marketplaceAccountHasProduct->marketplaceAccountId = $campaign->marketplaceAccount->id;
                $marketplaceAccountHasProduct->marketplaceId = $campaign->marketplaceAccount->marketplaceId;

                if(!$marketplaceAccountHasProductRepo->deleteProductFromMarketplaceAccount($marketplaceAccountHasProduct->printId())) {
                    $this->warning('Cycle','Could not delete a product',$marketplaceAccountHasProduct);
                }

                $marketplaceAccountHasProduct->nCos = $nCos;
                $marketplaceAccountHasProduct->cos = $cos;
                $marketplaceAccountHasProduct->actualSizes = $actualSizes;
                $marketplaceAccountHasProduct->iniSizes = $iniSizes;
                $marketplaceAccountHasProduct->cost = $one['cost'];
                $marketplaceAccountHasProduct->orderValue = $one['orderValue'];

                if (!isset($reportArray[$marketplaceAccountHasProduct->marketplaceAccount->printId()]))
                    $reportArray[$marketplaceAccountHasProduct->marketplaceAccount->printId()] = [];
                $reportArray[$marketplaceAccountHasProduct->marketplaceAccount->printId()][] = $marketplaceAccountHasProduct;
            }
        }

        $text = "";
        $actualMarketplaceAccount = null;
        foreach ($reportArray as $marketplaceAccountId => $marketplaceAccountHasProducts) {
            $this->debug('Cycle Report', 'Signaling Delete',
                ['key' => $marketplaceAccountId, 'val' => $marketplaceAccountHasProduct]);

            if ($marketplaceAccountId != $actualMarketplaceAccount) {
                $actualMarketplaceAccount = $marketplaceAccountId;
                $marketplaceAccount = \Monkey::app()->repoFactory->create('MarketplaceAccount')->findOneByStringId($actualMarketplaceAccount);
                $text .= (PHP_EOL . $marketplaceAccount->getCampaignName() . ': ' . PHP_EOL);
            }
            foreach ($marketplaceAccountHasProducts as $marketplaceAccountHasProduct) {
                $text .= str_pad($marketplaceAccountHasProduct->product->printId(),15,' ',STR_PAD_RIGHT)
                    . ' - ' . str_pad($marketplaceAccountHasProduct->product->printCpf(),35,' ',STR_PAD_RIGHT)
                    . ' - nCos: ' . str_pad( $marketplaceAccountHasProduct->nCos,6,' ',STR_PAD_RIGHT)
                    . ' - cos: ' . str_pad( $marketplaceAccountHasProduct->cos ,6,' ',STR_PAD_RIGHT)
                    . ' - disp: ' . str_pad( $marketplaceAccountHasProduct->actualSizes ,6,' ',STR_PAD_RIGHT)
                    . ' - prez: ' . str_pad( $marketplaceAccountHasProduct->product->getDisplayActivePrice() ,6,' ',STR_PAD_RIGHT)
                    . ' - cost: ' . str_pad( $marketplaceAccountHasProduct->cost ,6,' ',STR_PAD_RIGHT)
                    . ' - ord: ' . str_pad( $marketplaceAccountHasProduct->orderValue ,6,' ',STR_PAD_RIGHT)
                    . PHP_EOL;
            }
        }
        $this->debug('Cycle Report', 'Sending Email', $text);
        iwesMail('it@iwes.it',
            'Report Depubblicazione prodotti Marketplace '.\Monkey::app()->getName(),
            "Sono stati depubblicati i seguenti prodotti: " . PHP_EOL . $text);

    }
}