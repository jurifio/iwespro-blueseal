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
 * Class CDumpHistoryThomasEndJob
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
class CDumpHistoryThomasEndJob extends ACronJob
{

    /**
     * @param null $args
     */
    public function run($args = null)
    {
        $this->relevationProductStart();
        $this->report('CDumpHistoryThomasEndJob','start dump end history','');
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
            $files = glob('/home/iwespro/public_html/client/public/media/productsync/thomas/import/done/*.tar.gz');
        }
        $dateStart = (new \DateTime())->format('Y-m-d H:i:s');

        try {
            foreach ($files as $file) {
                $origingFile = basename($file,".gz") . PHP_EOL;
                $firstFileDay = substr($origingFile,-14,-12);
                $year = substr($origingFile,0,4);
                $yearEndSale = $year + 1;
                $month = substr($origingFile,4,2);
                $day = substr($origingFile,6,2);
                switch(true){
                    case $month=='05':
                        $montCompare='5';
                        break;
                    case $month=='06':
                        $montCompare='6';
                        break;
                    case $month=='07':
                        $montCompare='7';
                        break;
                    case $month=='08':
                        $montCompare='8';
                        break;
                    case $month=='09':
                        $montCompare='9';
                        break;
                    case $month=='01':
                        $montCompare='1';
                        break;
                    case $month=='02':
                        $montCompare='2';
                        break;
                    case $month=='03':
                        $montCompare='3';
                        break;
                    case $month=='04':
                        $montCompare='4';
                        break;
                }
                if (($firstFileDay == '20') && ($year>2019) && ($montCompare>5) ) {

                    $phar = new \PharData($file);
                    if (ENV == 'dev') {
                        $phar->extractTo('/media/sf_sites/iwespro/temp/',null,true);
                    } else {
                        $phar->extractTo('/home/iwespro/public_html/client/public/media/productsync/thomas/import/done/',null,true);
                    }
                    $nameFile = basename($file,".csv") . PHP_EOL;


                    $dateFile = (new \DateTime($year . '-' . $month . '-' . $day))->format('Y-m-d');
                    $dateStartSale1 = (new \DateTime($year . '-01-01'))->format('Y-m-d');
                    $dateEndSale1 = (new \DateTime($yearEndSale . '-03-15'))->format('Y-m-d');
                    $dateStartSale2 = (new \DateTime($year . '-07-01'))->format('Y-m-d');
                    $dateEndSale2 = (new \DateTime($year . '-09-15'))->format('Y-m-d');
                    echo '</br>' . $year . '<br>';
                    echo $day . '<br>';
                    echo $month . '<br>';

                    $fileexport = substr($nameFile,0,-8);

                    if (ENV == 'dev') {
                        $finalFile = '/media/sf_sites/iwespro/temp/' . substr($fileexport,15,100) . '.csv';
                    } else {
                        $finalFile = '/home/iwespro/public_html/client/public/media/productsync/thomas/import/done/' . substr($fileexport,15,100) . '.csv';
                    }



                    $f = fopen($finalFile,'r');
                    fgets($f);
                    $arrayProduct = [];
                    $dirtyProduct = '';
                    $quantity=0;
                    while (($values = fgetcsv($f,0,";")) !== false) {
                        $quantity = $values[30];
                        $barcode = $values[18];
                        $sql="select productId,productVariantId,productSizeId,barcode,shopId,priceActive,startQuantity from ProductSizeSoldDay where barcode='".$barcode."' and shopId=58 and 
                `year`='".$year."' and `month`='".$month."' and `day`='".$day."' ";
                        $res=\Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
                        if(count($res)>0) {
                            foreach ($res as $result) {
                                $productId = $result['productId'];
                                $productVariantId = $result['productVariantId'];
                                $productSizeId = $result['productSizeId'];
                                $priceActive = $result['priceActive'];
                                $startQuantity = $result['startQuantity'];
                            }

                            $productSold = $productSoldSizeRepo->findOneBy(['productId' => $productId,'productVariantId' => $productVariantId,'productSizeId' => $productSizeId,'shopId' => 58,'year' => $year,'month' => $month,'day' => $day]);
                            if ($productSold) {
                                $soldQuantity = $startQuantity - $quantity;
                                $productSold->startQuantity = $quantity;
                                $productSold->endQuantity = $quantity;
                                $netTotal = $priceActive * $soldQuantity;
                                $productSold->dateEnd = $year . '-' . $month . '-' . $day . ' 23:59:59';
                                $productSold->soldQuantity = $soldQuantity;
                                $productSold->netTotal = $netTotal;
                                $productSold->sourceUpgrade=$finalFile;
                                $productSold->update();
                            }else{
                                continue;
                            }

                        }
                    }
                    fclose($f);
                    unlink($finalFile);

                }else{
                    continue;
                }

            }
        } catch (\Throwable $e) {
            $this->report('CDumpHistoryThomasEndJob','Error',$e->getMessage());
        }





    }
}