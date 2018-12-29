<?php

namespace bamboo\blueseal\jobs;

use bamboo\domain\entities\CCartAbandonedEmailSend;
use bamboo\domain\repositories\CCartAbandonedEmailSendRepo;
use bamboo\domain\entities\COrder;
use bamboo\domain\entities\CCart;
use bamboo\domain\entities\CCartAbandonedEmailParam;
use bamboo\domain\entities\CCouponType;
use bamboo\domain\entities\CCoupon;
use bamboo\domain\entities\CCartLine;
use bamboo\core\base\CSerialNumber;
use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\blueseal\remote\readextdbtable\CReadExtDbTable;
use pdo;
use prepare;

use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CProductPublicSku;
use bamboo\domain\entities\CProduct;
use bamboo\core\events\AEventListener;


/**
 * Class CPrestashopGetProductIdEbayMarketPlaceJob
 * @package bamboo\blueseal\jobs
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
class CPrestashopGetProductIdEbayMarketPlaceJob extends ACronJob
{
    /**
     * @param null $args
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function run($args = null)
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



        $res="Allineamento reference ebay  eseguita  finita alle ore ". date('Y-m-d H:i:s');
        $this->report('Align  Ebay  reference to Pickyshop Export to Prestashop ',$res,$res);
        return $res;
    }


}