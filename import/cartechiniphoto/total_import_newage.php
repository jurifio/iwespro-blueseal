<?php

ini_set("display_errors",1);
error_reporting(~0);
require "/data/www/redpanda/htdocs/cartechinishop/BlueSeal.php";
use bamboo\core\db\pandaorm\adapter\CMySQLAdapter;
use bamboo\core\db\pandaorm\entities\CEntityManager;
use bamboo\import\photosToAmazon\CProductPhotoExport;

$BlueSeal = new BlueSeal('BlueSeal','cartechinishop','/data/www/redpanda');
$BlueSeal->enableDebugging();


$em = $BlueSeal->entityManagerFactory->create('Product');
/** @var CEntityManager $em */
$products = $em->findBySql("SELECT DISTINCT ps.productId as id,
                                            ps.productVariantId as productVariantId,
                                            ps.productSizeId as productSizeId

                                                                        from ProductSku ps , Product p
                            where Product.id = ProductSku.productId
                            and Product.productVariantId = ProductSku.productVariantId
                            AND Product.externalId != 0;",array());


$mysql = $BlueSeal->dbAdapter;
$BlueSeal->vendorLibraries->load("amazon2723");
$photoDir = "import/cartechiniphoto";

$credential = array(
    'key'   => 'AKIAJAT27PGJ6XWXBY6A',
    'secret'=> '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'
);
$export = new CProductPhotoExport($BlueSeal, $credential);

foreach($products as $product){
    /** @var CMySQLAdapter $mysql */

    ob_start();
    try {
        echo "------go------<br>";
        echo "Prodotto: " . $product->id . " var: " . $product->productVariantId . " cartechini: " . $product->externalId . "<br>";

        $res = $export->importFromAztecCode($product->id, $product->productVariantId);

        echo "abbinate " . $res . " foto". "<br>";
    } catch (Exception $e){
        echo "Prodotto: " . $product->id . " var: " . $product->productVariantId . " cartechini: " . $product->externalId. "<br>";
        echo "errore". "<br>";
        sleep(5);
    }
    echo "------end-----<br>";
    ob_end_flush();
    flush();
}



