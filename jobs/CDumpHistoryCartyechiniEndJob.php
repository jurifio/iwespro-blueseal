<?php

namespace bamboo\blueseal\jobs;

use bamboo\blueseal\marketplace\prestashop\CPrestashopProduct;
use bamboo\core\base\CObjectCollection;
use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CMarketplaceHasShop;
use bamboo\domain\entities\CPrestashopHasProduct;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductSku;
use bamboo\domain\entities\CProductSoldDay;
use PDO;
use DateTime;
use bamboo\amazon\business\builders\AAmazonFeedBuilder;
use bamboo\amazon\business\builders\CAmazonImageFeedBuilder;
use bamboo\amazon\business\builders\CAmazonInventoryFeedBuilder;
use bamboo\amazon\business\builders\CAmazonPricingFeedBuilder;
use bamboo\amazon\business\builders\CAmazonProductFeedBuilder;
use bamboo\amazon\business\builders\CAmazonRelationshipFeedBuilder;
use bamboo\core\application\AApplication;
use bamboo\domain\entities\CMarketplaceAccountHasProduct;

/**
 * Class CDumpHistoryCartyechiniEndJob
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 16/02/2021
 * @since 1.0
 */
class CDumpHistoryCartyechiniEndJob extends ACronJob
{

    /**
     * @param null $args
     */
    public function run($args = null)
    {
        $this->relevationProductStart();
        $this->report('CDumpHistoryCartyechiniEndJob','start day quantity check','');
    }

    /**
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    private function relevationProductStart()
    {
        $dirtySkuRepo = \Monkey::app()->repoFactory->create('DirtySku');
        $dirtyProductRepo = \Monkey::app()->repoFactory->create('DirtyProduct');
        $productSoldSizeRepo = \Monkey::app()->repoFactory->create('ProductSizeSoldDay');
        $shopHasProductRepo = \Monkey::app()->repoFactory->create('ShopHasProduct');
        if (ENV == 'dev') {
            $files = glob('/media/sf_sites/iwespro/temp/*.tar.gz');
        } else {
            $files = glob('/home/iwespro/public_html/client/public/media/productsync/cartechini/import/done/*.tar.gz');
        }
        $dateStart = (new \DateTime())->format('Y-m-d H:i:s');

        try {
            foreach ($files as $file) {
                $origingFile = basename($file,".tar.gz") . PHP_EOL;
                $year = substr($nameFile,0,4);
                $yearEndSale = $year + 1;
                $month = substr($nameFile,4,2);
                $day = substr($nameFile,6,2);
                $firstFileSku = substr($origingFile,15,4);

                if (($firstFileDay == '20') && ($firstFileSku=='SKUS') && ($year>2018)) {

                    $phar = new \PharData($file);
                    if (ENV == 'dev') {
                        $phar->extractTo('/media/sf_sites/iwespro/temp/',null,true);
                    } else {
                        $phar->extractTo('/home/iwespro/public_html/client/public/media/productsync/cartechini/import/done/',null,true);
                    }
                    $nameFile = basename($file,".csv") . PHP_EOL;


                    $dateFile = (new \DateTime($year . '-' . $month . '-' . $day))->format('Y-m-d');
                    $dateStartSale1 = (new \DateTime($year . '-01-01'))->format('Y-m-d');
                    $dateEndSale1 = (new \DateTime($yearEndSale . '-03-15'))->format('Y-m-d');
                    $dateStartSale2 = (new \DateTime($year . '-07-01'))->format('Y-m-d');
                    $dateEndSale2 = (new \DateTime($year . '-09-15'))->format('Y-m-d');


                    $fileexport = substr($nameFile,0,-8);

                    if (ENV == 'dev') {
                        $finalFile = '/media/sf_sites/iwespro/temp/' . substr($fileexport,15,100) . '.csv';
                    } else {
                        $finalFile = '/home/iwespro/public_html/client/public/media/productsync/cartechini/import/done/' . substr($fileexport,15,100) . '.csv';
                    }



                    $f = fopen($finalFile,'r');
                    fgets($f);
                    $arrayProduct = [];
                    $dirtyProduct = '';
                    $quantity = 0;
                    $lineCount = 0;
                    while (($values = fgetcsv($f,0,";",'|')) !== false) {
                        if ($lineCount > 0) {
                            $quantity = $values[3];
                            $size = $values[2];
                            $extId = $values[9];
                            $var = $values[10];
                            $sql = "select ps.productId as productId,ps.productVariantId as productVariantId,
                    ps.productSizeId as productSizeId, ds.barcode as barcode, ps.shopId as shopId, ps.priceActive as priceActive,
                    ps.startQuantity as startQuantity from ProductSizeSoldDay ps join DirtyProduct dp on ps.productId=dp.productId and ps.productVariantId=dp.productVariantId and 
                                                                                        ps.shopId=dp.shopId 
join DirtySku ds on dp.id=ds.dirtyProductId where dp.extId='" . $extId . "' and ds.shopId=1 and ds.size='" . $size . "' and  
                `ps`.`year`='" . $year . "' and `ps`.`month`='" . $month . "' and `ps`.`day`='" . $day . "' ";
                            $res = \Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
                            if (count($res) > 0) {
                                foreach ($res as $result) {
                                    $productId = $result['productId'];
                                    $productVariantId = $result['productVariantId'];
                                    $productSizeId = $result['productSizeId'];
                                    $priceActive = $result['priceActive'];
                                    $startQuantity = $result['startQuantity'];
                                }
                                $productSold = $productSoldSizeRepo->findOneBy(['productId' => $productId,'productVariantId' => $productVariantId,'productSizeId' => $productSizeId,'shopId' => 1,'year' => $year,'month' => $month,'day' => $day]);
                                if ($productSold) {
                                    $soldQuantity = $startQuantity - $quantity;
                                    $productSold->startQuantity = $quantity;
                                    $productSold->endQuantity = $quantity;
                                    $netTotal = $priceActive * $soldQuantity;
                                    $productSold->soldQuantity = $soldQuantity;
                                    $productSold->netTotal = $netTotal;
                                    $productSold->sourceUpgrade = $finalFile;
                                    $productSold->update();
                                }

                            }
                        }
                        $lineCount++;
                    }
                    fclose($f);
                    unlink($finalFile);
                }

            }
        } catch (\Throwable $e) {
            $this->report('CDumpHistorycartechiniEndJob','Error',$e->getMessage());
        }








    }
}