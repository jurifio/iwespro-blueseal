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
 * Class CProductSoldDayStartJob
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
class CDumpHistoryThomasStartJob extends ACronJob
{

    /**
     * @param null $args
     */
    public function run($args = null)
    {
        $this->relevationProductStart();
        $this->report('CDumpHistoryThomasStartJob','start dump check','');
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

                if ($firstFileDay == '01') {
                    $phar = new \PharData($file);
                    if (ENV == 'dev') {
                        $phar->extractTo('/media/sf_sites/iwespro/temp/',null,true);
                    } else {
                        $phar->extractTo('/home/iwespro/public_html/client/public/media/productsync/thomas/import/done/',null,true);
                    }

                    $nameFile = basename($file,".csv") . PHP_EOL;

                    $year = substr($nameFile,0,4);
                    $yearEndSale = $year + 1;
                    $month = substr($nameFile,4,2);
                    $day = substr($nameFile,6,2);
                    $dateFile = (new \DateTime($year . '-' . $month . '-' . $day))->format('Y-m-d');
                    $dateStartSale1 = (new \DateTime($year . '-01-01'))->format('Y-m-d');
                    $dateEndSale1 = (new \DateTime($yearEndSale . '-03-15'))->format('Y-m-d');
                    $dateStartSale2 = (new \DateTime($year . '-07-01'))->format('Y-m-d');
                    $dateEndSale2 = (new \DateTime($year . '-09-15'))->format('Y-m-d');


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
                    $quantity = 0;
                    while (($values = fgetcsv($f,0,";")) !== false) {

                        $quantity = $values[30];

                        $dirtySku = $dirtySkuRepo->findOneBy(['barcode' => $values[18],'shopId' => 58]);
                        if ($dirtySku) {
                            if ($dirtySku->productSizeId != null) {
                                $dirtyProductId = $dirtySku->dirtyProductId;

                                $dirtyProduct = $dirtyProductRepo->findOneBy(['id' => $dirtyProductId]);
                                if ($dirtyProduct) {
                                    if ($dirtyProduct->productId != null) {
                                        $productId = $dirtyProduct->productId;
                                        $productVariantId = $dirtyProduct->productVariantId;
                                        $shopHasProduct = $shopHasProductRepo->findOneBy(['productId' => $productId,'productVariantId' => $productVariantId,'shopId' => 58]);
                                        $productSold = $productSoldSizeRepo->findOneBy(['productId' => $productId,'productVariantId' => $productVariantId,'productSizeId' => $dirtySku->productSizeId,'shopId' => 58,'year' => $year,'month' => $month,'day' => $day]);
                                        if ($productSold == null) {
                                            $productSoldInsert = $productSoldSizeRepo->getEmptyEntity();
                                            $productSoldInsert->productId = $productId;
                                            $productSoldInsert->productVariantId = $productVariantId;
                                            $productSoldInsert->productSizeId = $dirtySku->productSizeId;
                                            $productSoldInsert->shopId = 58;
                                            $productSoldInsert->barcode = $values[18];
                                            $productSoldInsert->dateStart = $year . '-' . $month . '-' . $day . ' 00:00:00';
                                            $productSoldInsert->startQuantity = $quantity;
                                            $productSoldInsert->dateEnd = $year . '-' . $month . '-' . $day . ' 00:00:00';
                                            $productSoldInsert->endQuantity = $quantity;
                                            if ($dateFile >= $dateStartSale1 && $dateFile <= $dateEndSale1) {
                                                if ($shopHasProduct->salePrice == null) {
                                                    $priceActive = $values[39];
                                                } else {
                                                    $priceActive = $shopHasProduct->salePrice;

                                                }
                                            } else {
                                                if ($shopHasProduct->price == null) {
                                                    $priceActive = $values[39];
                                                } else {
                                                    $priceActive = $shopHasProduct->price;

                                                }

                                            }
                                            if ($dateFile >= $dateStartSale2 && $dateFile <= $dateEndSale2) {
                                                if ($shopHasProduct->salePrice == null) {
                                                    $priceActive = $values[39];

                                                } else {
                                                    $priceActive = $shopHasProduct->salePrice;

                                                }
                                            } else {
                                                if ($shopHasProduct->price == null) {
                                                    $priceActive = $values[39];

                                                } else {
                                                    $priceActive = $shopHasProduct->price;

                                                }

                                            }
                                            $productSoldInsert->priceActive = $priceActive;
                                            $productSoldInsert->soldQuantity = 0;
                                            $productSoldInsert->netTotal = 0;
                                            $productSoldInsert->day = $day;
                                            $productSoldInsert->month = $month;
                                            $productSoldInsert->year = $year;
                                            $productSoldInsert->sourceInitial = $finalFile;
                                            $productSoldInsert->insert();
                                        }


                                    } else {
                                        continue;
                                    }
                                } else {
                                    continue;
                                }
                            }

                        }
                    }
                    fclose($f);
                    unlink($finalFile);
                } else {
                    continue;
                }
            }
        } catch (\Throwable $e) {
            $this->report('CDumpHistoryThomasStartJob','Error',$e->getMessage());
        }
    }
}