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
                        count(DISTINCT cvho.orderId) AS orderCount
                FROM Campaign c
                  JOIN CampaignVisit cv ON c.id = cv.campaignId
                  JOIN CampaignVisitHasProduct cvhp ON (cv.id, cv.campaignId) = (cvhp.campaignVisitId, cvhp.campaignId)
                  LEFT JOIN CampaignVisitHasOrder cvho ON (cv.id, cv.campaignId) = (cvho.campaignVisitId, cvho.campaignId)
                WHERE cv.timestamp > (NOW() - INTERVAL 1 WEEK)
                GROUP BY c.id,cvhp.productId,cvhp.productVariantId
                HAVING cost > 0
                ORDER BY c.id ASC";

        $res = \Monkey::app()->dbAdapter->query($sql, []);
        /** @var CProductRepo $productRepo */
        $productRepo = \Monkey::app()->repoFactory->create('Product');
        /** @var CCampaignRepo $campaingRepo */
        $campaingRepo = \Monkey::app()->repoFactory->create('Campaign');
        /** @var CMarketplaceAccountHasProductRepo $marketplaceAccountHasProduct */
        $marketplaceAccountHasProduct = \Monkey::app()->repoFactory->create('MarketplaceAccountHasProduct');
        $reportArray = [];
        foreach ($res as $one) {
            /** @var CProduct $product */
            $product = $productRepo->findOneBy(["id" => $one['productId'], "productVariantId" => $one['productVariantId']]);
            /** @var CCampaign $campaign */
            $campaign = $campaingRepo->findOneBy(["id" => $one['id']]);
            if (!$campaign->marketplaceAccount) {
                $this->warning('Cycle', 'Campaign not linked with marketplace', $one);
            }
            $cos = $one['cost'] / (($one['orderCount'] == 0 ? 1 : $one['orderCount']) * $product->getDisplayActivePrice()) * 100;

            if ($cos > $campaign->marketplaceAccount->getConfig()['maxCos'] ?? 15) {
                $this->report('Cycle', "Deleting product from Marketplace, cos: $cos, over maxCos: " . $config['maxCos'], ['product' => $product, 'campaing' => $campaign]);
                /** @var CMarketplaceAccountHasProduct $marketplaceAccountHasProduct */
                $marketplaceAccountHasProduct->getEmptyEntity();
                $marketplaceAccountHasProduct->productId = $product->id;
                $marketplaceAccountHasProduct->productVariantId = $product->productVariantId;
                $marketplaceAccountHasProduct->marketplaceAccountId = $campaign->marketplaceAccount->id;
                $marketplaceAccountHasProduct->marketplaceId = $campaign->marketplaceAccount->marketplaceId;
                $marketplaceAccountHasProduct->deleteProductFromMarketplaceAccount($marketplaceAccountHasProduct);
                $marketplaceAccountHasProduct->cos = $cos;
                if (!isset($reportArray[$marketplaceAccountHasProduct->marketplaceAccount->printId()]))
                    $reportArray[$marketplaceAccountHasProduct->marketplaceAccount->printId()] = [];
                $reportArray[$marketplaceAccountHasProduct->marketplaceAccount->printId()][] = $marketplaceAccountHasProduct;
            }

            $text = "";
            $actualMarketplaceAccount = null;
            foreach ($reportArray as $marketplaceAccountId => $marketplaceAccountHasProduct) {
                if ($marketplaceAccountId != $actualMarketplaceAccount) {
                    $text .= PHP_EOL . $marketplaceAccountHasProduct->marketplaceAccount->getCampaignName() . ': ' . PHP_EOL;
                    $actualMarketplaceAccount = $marketplaceAccountId;
                }
                $text .= $marketplaceAccountHasProduct->product->printId() . ' - ' . $marketplaceAccountHasProduct->product->printCpf() . ' - cos: ' . $marketplaceAccountHasProduct->cos . PHP_EOL;
            }
            iwesMail('support@iwes.it',
                'Report Depubblicazione produtti Marketplace',
                "Sono stati depubblicati i seguenti prodotti: " . PHP_EOL . $text);
        }

    }
}