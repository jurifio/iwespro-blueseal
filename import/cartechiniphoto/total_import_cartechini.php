<?php

ini_set("display_errors",1);
error_reporting(~0);
//require "/data/www/redpanda/htdocs/cartechinishop/BlueSeal.php";
use bamboo\core\db\pandaorm\adapter\CMySQLAdapter;
use bamboo\core\db\pandaorm\entities\CEntityManager;
use bamboo\import\photosToAmazon\CProductPhotoExport;

//$ninetyNineMonkey = new BlueSeal('BlueSeal','cartechinishop','/data/www/redpanda');
//$ninetyNineMonkey->enableDebugging();


$em = $ninetyNineMonkey->entityManagerFactory->create('Product');
/** @var CEntityManager $em */
$products = $em->findBySql("SELECT DISTINCT ProductSku.productId as id,
                                            ProductSku.productVariantId as productVariantId
                            from ProductSku, Product
                            where Product.id = ProductSku.productId
                            and Product.productVariantId = ProductSku.productVariantId
                            AND Product.externalId != 0;",array());


$mysql = $ninetyNineMonkey->dbAdapter;
$ninetyNineMonkey->vendorLibraries->load("amazon2723");
$photoDir = "import/cartechiniphoto";

$credential = array(
    'key'   => 'AKIAJAT27PGJ6XWXBY6A',
    'secret'=> '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'
);
$export = new CProductPhotoExport($ninetyNineMonkey, $credential);

foreach($products as $product){
    /** @var CMySQLAdapter $mysql */

    ob_start();
    try {
        echo "------go------<br>";
        echo "Prodotto: " . $product->id . " var: " . $product->productVariantId . " cartechini: " . $product->externalId . "<br>";

        $res = $export->importFromCartechiniCode($product->id, $product->productVariantId);

        echo "abbinate " . $res . " foto". "<br>";
    } catch (Exception $e){
        echo "Prodotto: " . $product->id . " var: " . $product->productVariantId . " cartechini: " . $product->externalId. "<br>";
        echo "errore". "<br>";
        sleep(5);
    }
    echo "------end-----<br>";
    ob_end_flush();
    flush();
    sleep(1);
}



