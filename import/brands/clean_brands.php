<?php

ini_set("display_errors",1);
error_reporting(~0);
//require "/data/www/redpanda/htdocs/cartechinishop/BlueSeal.php";
use bamboo\core\db\pandaorm\adapter\CMySQLAdapter;
use bamboo\core\db\pandaorm\entities\CEntityManager;
use bamboo\core\utils\slugify\CSlugify;

//$ninetyNineMonkey = new BlueSeal('BlueSeal','cartechinishop','/data/www/redpanda');
//$ninetyNineMonkey->enableDebugging();

function nvl($data, $alter){
    if(isset($data) &&!empty($data)) return $data;
    return $alter;
}

$slugify = new CSlugify();

$em = $ninetyNineMonkey->entityManagerFactory->create('ProductBrand');
/** @var CEntityManager $em */
$brands = $em->findAll("","");


foreach($brands as $brand){
    $slug = $slugify->slugify($brand->name);
    $ninetyNineMonkey->dbAdapter->update("ProductBrand",array("slug"=>$slug),array("id"=>$brand->id));
}

