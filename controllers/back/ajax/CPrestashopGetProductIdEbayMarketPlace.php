<?php

namespace bamboo\controllers\back\ajax;


use bamboo\blueseal\remote\readextdbtable\CReadExtDbTable;
use PrestaShopWebservice;
use PrestaShopWebserviceException;
use bamboo\controllers\back\ajax\CPrestashopGetImage;
use PDO;
use prepare;

use bamboo\core\exceptions\BambooConfigException;
use bamboo\core\base\CObjectCollection;
use bamboo\utils\time\STimeToolbox;

/**
 * Class CPrestashopGetProductIdEbayMarketPlace
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 29/12/2018
 * @since 1.0
 */



class CPrestashopGetProductIdEbayMarketPlace extends AAjaxController
{


    /**
     * @return string
     *
     * @throws \bamboo\core\exceptions\BambooDBALException
     * @throws \bamboo\core\exceptions\BambooException
     */
    public function post()
    {

        set_time_limit(0);
        ini_set('memory_limit', '2048M');
        /* @var $productId*/
        /* @var $productVariantId */
        $productId='';
        $productVariantId='';


        /******* apertura e creazione file csv per espostazione********/
        $db_host = "iwes.shop";
        $db_name = "iwesshop_pres848";
        $db_user = "iwesshop_pres848";
        $db_pass = "@5pM5S)Mn8";
        define("HOST", "iwes.shop");
        define("USERNAME", "iwesshop_pres848");
        define("PASSWORD", "@5pM5S)Mn8");
        define("DATABASE", "iwesshop_pres848");
        $res = "";
        try {

            $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_user, $db_pass);
            $db_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $res .= " connessione ok <br>";
        } catch (PDOException $e) {
            $res .= $e->getMessage();
        }



        $stmtGetProduct = $db_con->prepare("SELECT id_product as prestaId, id_product_ref as marketplaceProductId , id_shop as shopId  FROM psz6_fastbay1_product");

        $stmtGetProduct->execute();
        while ($rowGetProduct = $stmtGetProduct->fetch(PDO::FETCH_ASSOC)) {
            $prestashopProductId = $rowGetProduct['prestaId'];
            $marketplaceProductId = $rowGetProduct['marketplaceProductId'];
            $shopId=$rowGetProduct['shopId'];
            $findMarketplaceHasShop=\Monkey::app()->repoFactory->create('MarketplaceHasShop')->findOneBy(['marketplaceId'=>3,'prestashopId'=>$shopId]);
            $shopmarketplaceId=$findMarketplaceHasShop->id;
            $updateMarketplaceHasProductAssociate=\Monkey::app()->repoFactory->create('MarketplaceHasProductAssociate')->findOneBy(['id'=>$prestashopProductId,'marketplaceId'=>3,'marketPlaceHasShopId'=>$shopmarketplaceId]);
            $updateMarketplaceHasProductAssociate->marketplaceProductId=$marketplaceProductId;
            $updateMarketplaceHasProductAssociate->update();
        }
                    $res="Allineamento Id Eay Eseguito";
        return $res;
    }
}

          




