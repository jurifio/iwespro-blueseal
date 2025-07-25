<?php


namespace bamboo\controllers\back\ajax;


use bamboo\domain\entities\CCampaign;
use bamboo\domain\entities\CMarketplaceAccountHasProduct;
use bamboo\domain\entities\CProduct;
use bamboo\domain\repositories\CCampaignRepo;
use bamboo\domain\repositories\CMarketplaceAccountHasProductRepo;
use bamboo\domain\repositories\CProductRepo;
use PDO;
use PDOException;

class CDepublishMarketplaceProductAjaxController extends AAjaxController
{
    public function get()
    {

    }
    public function post()
    {
        set_time_limit(0);
        ini_set('memory_limit', '2048M');

        $res = "";
        $shopRepo = \Monkey ::app() -> repoFactory -> create('Shop') -> findBy(['hasEcommerce' => 1]);

        foreach ($shopRepo as $value) {
            $this -> report('Start ImportOrder From PickySite ', 'Shop To Import' . $value -> name);
            /********marketplace********/
            $db_host = $value -> dbHost;
            $db_name = $value -> dbName;
            $db_user = $value -> dbUsername;
            $db_pass = $value -> dbPassword;
            $shop = $value -> id;
            try {

                $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_user, $db_pass);
                $db_con -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $res .= " connessione ok <br>";
            } catch (PDOException $e) {
                $res .= $e -> getMessage();
            }
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

            $res = $db_con ->prepare($sql, []);
            $res->execute();
            /** @var CProductRepo $productRepo */
            $productRepo = \Monkey ::app() -> repoFactory -> create('Product');
            /** @var CCampaignRepo $campaingRepo */
            $campaingRepo = \Monkey ::app() -> repoFactory -> create('Campaign');

            /** @var CMarketplaceAccountHasProductRepo $marketplaceAccountHasProductRepo */
            $marketplaceAccountHasProductRepo = \Monkey ::app() -> repoFactory -> create('MarketplaceAccountHasProduct');
            //creo il report
            $reportArray = [];
            \Monkey ::app() -> applicationLog('run DepublishCMarketplaceProductAjaxController', 'Report', 'Starting Cycle for ' . count($res), '');
            // per ogni risultato nella query
            foreach ($res as $one) {
                // cerco il prodotto
                /** @var CProduct $product */
                $product = $productRepo -> findOneBy(["id" => $one['productId'], "productVariantId" => $one['productVariantId']]);
                //cerco la campagna che è presente nei dati
                /** @var CCampaign $campaign */
                $productSizeGroupId = $product -> productSizeGroupId;
                $campaign = $campaingRepo -> findOneBy(["id" => $one['id']]);
                // verifico se la campagna è lincata al marketplace
                if (!$campaign -> marketplaceAccount) {
                    \Monkey ::app() -> applicationLog('Cycle DepublishCMarketplaceProductAjaxController', 'Warning', 'Campaign not linked with marketplace', $one);
                }
                $iniSizes = $product -> productSku -> count();
                $actualSizes = 0;
                foreach ($product -> productSku as $sku) if ($sku -> stockQty > 0) $actualSizes++;

                \Monkey ::app() -> applicationLog('Cycle Res DepublishCMarketplaceProductAjaxController', 'Report ', 'Doing Math',
                    [
                        'iniSizes' => $iniSizes,
                        'actualSizes' => $actualSizes,
                        'cost' => $one['cost'],
                        'orderCount' => $one['orderCount'],
                        'productPrice' => $product -> getDisplayActivePrice()
                    ]);
                $checkIfProductSizeGroupId1 = isset($campaign -> marketplaceAccount -> getConfig()['productSizeGroup1']) ? $campaign -> marketplaceAccount -> getConfig()['productSizeGroup1'] : 0;
                $checkIfProductSizeGroupId2 = isset($campaign -> marketplaceAccount -> getConfig()['productSizeGroup2']) ? $campaign -> marketplaceAccount -> getConfig()['productSizeGroup2'] : 0;
                if ($productSizeGroupId1 == $productSizeGroupId) {
                    $multiplierIs = $campaign -> marketplaceAccount -> getConfig()['valueexcept1'];
                } elseif ($productSizeGroupId2 == $productSizeGroupId) {
                    $multiplierIs = $campaign -> marketplaceAccount -> getConfig()['valueexcept1'];
                } else {
                    $multiplierIs = isset($campaign -> marketplaceAccount -> getConfig()['multiplierDefault']) ? $campaign -> marketplaceAccount -> getConfig()['multiplierDefault'] : 0.1;
                }


                $sizeFill = $actualSizes / $iniSizes;

                if ($sizeFill === 0) $nCos = 'NaN';
                else $nCos = round((
                        $one['cost'] /
                        (
                            ($one['orderCount'] == 0 ? 0.1 : $one['orderCount']) * $product -> getDisplayActivePrice()
                        )
                        * $sizeFill
                    ) * 100, 2);
                // costo cpc fratto il conteggio degli ordini con quel prodotto moltiplicato il prezzo attivo  moltiplicato la giacenza media moltiplicato per 100
                if ($one['orderCount'] == 0) $cos = 'NaN';
                else $cos = round($one['cost'] / $one['orderValue'] * 100, 2);
                /** costo campagna  / somma totale degli ordini per cento   */
                \Monkey ::app() -> ApplicationLog('Cycle Res DepublishCMarketplaceProductAjaxController', 'Report', 'Math Done', [
                    'cos' => $cos,
                    'nCos' => $nCos
                ]);
                //definizione del massimo costo per giorno in base alla query
                $maxCos = $campaign -> marketplaceAccount -> getConfig()['maxCos'] ?? 7;
                \Monkey ::app() -> applicationLog('Cycle DepublishCMarketplaceProductAjaxController', 'Report', "Define  cos: $nCos, and maxCos: " . $maxCos, ['product' => $product, 'campaing' => $campaign]);
                if ($nCos === 'NaN' || $nCos > $maxCos) {
                    $this -> report('Cycle', "Deleting product from Marketplace, cos: $nCos, over maxCos: " . $maxCos, ['product' => $product, 'campaing' => $campaign]);
                    /** @var CMarketplaceAccountHasProduct $marketplaceAccountHasProduct */
                    $marketplaceAccountHasProduct = $marketplaceAccountHasProductRepo -> getEmptyEntity();
                    $marketplaceAccountHasProduct -> productId = $product -> id;
                    $marketplaceAccountHasProduct -> productVariantId = $product -> productVariantId;
                    $marketplaceAccountHasProduct -> marketplaceAccountId = $campaign -> marketplaceAccount -> id;
                    $marketplaceAccountHasProduct -> marketplaceId = $campaign -> marketplaceAccount -> marketplaceId;

                    if (!$marketplaceAccountHasProductRepo -> deleteProductFromMarketplaceAccount($marketplaceAccountHasProduct -> printId())) {
                        $this -> warning('Cycle', 'Could not delete a product', $marketplaceAccountHasProduct);
                    }

                    $marketplaceAccountHasProduct -> nCos = $nCos;
                    $marketplaceAccountHasProduct -> cos = $cos;
                    $marketplaceAccountHasProduct -> actualSizes = $actualSizes;
                    $marketplaceAccountHasProduct -> iniSizes = $iniSizes;
                    $marketplaceAccountHasProduct -> cost = $one['cost'];
                    $marketplaceAccountHasProduct -> orderValue = $one['orderValue'];

                    if (!isset($reportArray[$marketplaceAccountHasProduct -> marketplaceAccount -> printId()]))
                        $reportArray[$marketplaceAccountHasProduct -> marketplaceAccount -> printId()] = [];
                    $reportArray[$marketplaceAccountHasProduct -> marketplaceAccount -> printId()][] = $marketplaceAccountHasProduct;
                }
            }

            $text = "";
            $actualMarketplaceAccount = null;
            foreach ($reportArray as $marketplaceAccountId => $marketplaceAccountHasProducts) {
                $this -> debug('Cycle Report', 'Signaling Delete',
                    ['key' => $marketplaceAccountId, 'val' => $marketplaceAccountHasProduct]);

                if ($marketplaceAccountId != $actualMarketplaceAccount) {
                    $actualMarketplaceAccount = $marketplaceAccountId;
                    $marketplaceAccount = \Monkey ::app() -> repoFactory -> create('MarketplaceAccount') -> findOneByStringId($actualMarketplaceAccount);
                    $text .= (PHP_EOL . $marketplaceAccount -> getCampaignName() . ': ' . PHP_EOL);
                }
                foreach ($marketplaceAccountHasProducts as $marketplaceAccountHasProduct) {
                    $text .= str_pad($marketplaceAccountHasProduct -> product -> printId(), 15, ' ', STR_PAD_RIGHT)
                        . ' - ' . str_pad($marketplaceAccountHasProduct -> product -> printCpf(), 35, ' ', STR_PAD_RIGHT)
                        . ' - nCos: ' . str_pad($marketplaceAccountHasProduct -> nCos, 6, ' ', STR_PAD_RIGHT)
                        . ' - cos: ' . str_pad($marketplaceAccountHasProduct -> cos, 6, ' ', STR_PAD_RIGHT)
                        . ' - disp: ' . str_pad($marketplaceAccountHasProduct -> actualSizes, 6, ' ', STR_PAD_RIGHT)
                        . ' - prez: ' . str_pad($marketplaceAccountHasProduct -> product -> getDisplayActivePrice(), 6, ' ', STR_PAD_RIGHT)
                        . ' - cost: ' . str_pad($marketplaceAccountHasProduct -> cost, 6, ' ', STR_PAD_RIGHT)
                        . ' - ord: ' . str_pad($marketplaceAccountHasProduct -> orderValue, 6, ' ', STR_PAD_RIGHT)
                        . PHP_EOL;
                }
            }
            $this -> debug('Cycle Report', 'Sending Email', $text);
            iwesMail('it@iwes.it',
                'Report Depubblicazione prodotti Marketplace ' . \Monkey ::app() -> getName(),
                "Sono stati depubblicati i seguenti prodotti: " . PHP_EOL . $text);


        }
    }

    public function delete(){

    }


}