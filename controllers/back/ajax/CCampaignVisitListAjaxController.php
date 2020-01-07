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
        $sample = \Monkey::app()->repoFactory->create('Campaign')->getEmptyEntity();

        $query = "SELECT c.id,
                        c.name as campaignName,
                        c.code as campaignCode,
						cvhp.campaignVisitId as CampaignVisitId,
                        cv.timestamp as campaignVisit,
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
                FROM Campaign c
                  JOIN CampaignVisit cv ON c.id = cv.campaignId
                    left join Shop s on c.remoteShopId =s.id
                  JOIN CampaignVisitHasProduct cvhp ON (cv.id, cv.campaignId) = (cvhp.campaignVisitId, cvhp.campaignId)
                  JOIN MarketplaceAccountHasProduct mahp on (cvhp.productId=mahp.productId and cvhp.productVariantId=mahp.productVariantId)
                  
                  LEFT JOIN (
                    CampaignVisitHasOrder cvho JOIN OrderLine ol ON cvho.orderId = ol.orderId
                  ) ON (cv.id, cv.campaignId) = (cvho.campaignVisitId, cvho.campaignId) AND 
                    (cvhp.productId,cvhp.productVariantId) = (ol.productId,ol.productVariantId)
                WHERE cv.timestamp > (NOW() - INTERVAL 1 WEEK)
                GROUP BY c.id,cvhp.productId,cvhp.productVariantId
                HAVING cost > 0
                ORDER BY c.id ASC";



        $timeFrom = \DateTime::createFromFormat('Y-m-d', $this->app->router->request()->getRequestData('startDate'));
        $timeTo = \DateTime::createFromFormat('Y-m-d', $this->app->router->request()->getRequestData('endDate'));
        $timeFrom = $timeFrom ? $timeFrom->format('Y-m-d') : null;
        $timeTo = $timeTo ? $timeTo->format('Y-m-d') : null;
        $queryParameters = [$timeFrom, $timeTo];

        $datatable = new CDataTables($query, $sample->getPrimaryKeys(), $_GET, true);
        $productRepo = \Monkey::app()->repoFactory->create('Product');
        /** @var CCampaignRepo $campaingRepo */
        $campaingRepo = \Monkey::app()->repoFactory->create('Campaign');

        /** @var CMarketplaceAccountHasProductRepo $marketplaceAccountHasProductRepo */
        $marketplaceAccountHasProductRepo = \Monkey::app()->repoFactory->create('MarketplaceAccountHasProduct');
        $campaigns = $this->app->dbAdapter->query($datatable->getQuery(false, true), array_merge($datatable->getParams()))->fetchAll();
        $count = $sample->em()->findCountBySql($datatable->getQuery(true), array_merge($queryParameters, $datatable->getParams()));
        $totalCount = $sample->em()->findCountBySql($datatable->getQuery('full'), array_merge($queryParameters, $datatable->getParams()));
        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        foreach ($campaigns as $campaignData) {
            // cerco il prodotto
            /** @var CProduct $product */
            $product = $productRepo->findOneBy(["id" => $campaignData['productId'], "productVariantId" => $campaignData['productVariantId']]);
            //cerco la campagna che è presente nei dati
            /** @var CCampaign $campaign */
            $productSizeGroupId=$product->productSizeGroupId;
            $campaign = $campaingRepo->findOneBy(["id" => $campaignData['id']]);
            // verifico se la campagna è lincata al marketplace
            $iniSizes = $product->productSku->count();
            $actualSizes = 0;
            foreach ($product->productSku as $sku) if ($sku->stockQty > 0) $actualSizes++;
            $checkIfProductSizeGroupId1=isset($campaign->marketplaceAccount->getConfig()['productSizeGroup1'])? $campaign->marketplaceAccount->getConfig()['productSizeGroup1']:0;
            $checkIfProductSizeGroupId2=isset($campaign->marketplaceAccount->getConfig()['productSizeGroup2'])? $campaign->marketplaceAccount->getConfig()['productSizeGroup2']:0;
            if($checkIfProductSizeGroupId1==$productSizeGroupId){
                $multiplierIs=$campaign->marketplaceAccount->getConfig()['valueexcept1'];
            }elseif($checkIfProductSizeGroupId2==$productSizeGroupId){
                $multiplierIs=$campaign->marketplaceAccount->getConfig()['valueexcept2'];
            }else{
                $multiplierIs=isset($campaign->marketplaceAccount->getConfig()['multiplierDefault'])? $campaign->marketplaceAccount->getConfig()['multiplierDefault']:0.1;
            }


            $sizeFill = $actualSizes / $iniSizes;

            if ($sizeFill === 0) $nCos = 'NaN';
            else $nCos = round((
                    $campaignData['cost'] /
                    (
                        ($campaignData['orderCount'] == 0 ? 0.1 : $one['orderCount']) * $product->getDisplayActivePrice()
                    )
                    * $sizeFill
                ) * 100,2);
            // costo cpc fratto il conteggio degli ordini con quel prodotto moltiplicato il prezzo attivo  moltiplicato la giacenza media moltiplicato per 100
            if ($campaignData['orderCount'] == 0) $cos = 'NaN';
            else $cos = round($campaignData['cost'] / $campaignData['orderValue'] * 100,2);
            /** costo campagna  / somma totale degli ordini per cento   */

            //definizione del massimo costo per giorno in base alla query
            $maxCos = $campaign->marketplaceAccount->getConfig()['maxCos'] ?? 7;
            if ($nCos === 'NaN' || $nCos > $maxCos) {
                $messageDelete="Deleting product from Marketplace, cos: $nCos, over maxCos: " . $maxCos;

            }else{
                $messageDelete='';
            }




            $row['id'] = $campaignData['id'];
            $row['campaignCode'] = $campaignData['campaignCode'];
            $row['campaignName'] = $campaignData['campaignName'];
            $row['campaignVisit'] = $campaignData['campaignVisit'];
            $row['codeProduct'] = $campaignData['codeProduct'];
            $row['defaultCpc'] = $campaignData['defaultCpc'];
            $row['shopName'] = $campaignData['shopName'];
            $row['visits'] = $campaignData['visits'];
            $row['cost'] = $campaignData['cost'];
            $row['orderCount'] = $campaignData['orderCount'];
            $row['orderValue'] = $campaignData['orderValue'];
            $row['priceModifier'] = $campaignData['priceModifier'];
            $row['cos']=$cos;
            $row['maxCos']=$maxCos;
            $row['sizeFill']=$sizeFill;
            $row['messageDelete']=$messageDelete;
            $row['multiplierIs']=$multiplierIs;

            $response['data'][] = $row;
        }

        return json_encode($response);
    }
}