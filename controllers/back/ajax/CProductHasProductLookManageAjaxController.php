<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\exceptions\BambooException;
use bamboo\core\intl\CLang;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CProductHasProductCorrelation;


/**
 * Class CProductHasProductCorrelationManageAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 15/06/2020
 * @since 1.0
 */
class CProductHasProductLookManageAjaxController extends AAjaxController
{
    public function get()
    {
        $look = [];
        $shopRepo=\Monkey::app()->repoFactory->create('Shop');
        $productLook = \Monkey::app()->repoFactory->create('ProductLook')->findAll();
        foreach ($productLook as $collect) {
            $shop=$shopRepo->findOneBy(['id'=>$collect->remoteShopId]);

            if ($collect->image != null) {
                $image = $collect->image;
            } else {
                $image = '';
            }
            array_push($look,['id' => $collect->id,'name' => $collect->name,'img' => $image,'shopId'=>$shop->id,'shopName'=>$shop->name]);
        }

        return json_encode($look);

    }

    public function post()
    {
        $res = '';
        $data = \Monkey::app()->router->request()->getRequestData();
        $shopHasProductRepo = \Monkey::app()->repoFactory->create('ShopHasProduct');
        $productHasProductLookRepo = \Monkey::app()->repoFactory->create('ProductHasProductLook');

        $code = $data['code'];
        $products = $data['row'];
        foreach ($products as $product) {
            $prod = explode('-',$product);
            $productId = $prod[0];
            $productVariantId = $prod[1];
            $shopId = $shopHasProductRepo->findOneBy(['productId' => $productId,'productVariantId' => $productVariantId])->shopId;
            $pl=\Monkey::app()->repoFactory->create('ProductLook')->findOneBy(['id'=>$code]);
            $remoteLookId=$pl->remoteId;
            $remoteShopId=$pl->remoteShopId;
            $findProduct = $productHasProductLookRepo->findOneBy(['productLookId' => $code,'productId' => $productId,'productVariantId' => $productVariantId,'shopId' => $shopId]);
            if ($findProduct == null) {
                $productLook = $productHasProductLookRepo->getEmptyEntity();
                $productLook->productLookId = $code;
                $productLook->productId = $productId;
                $productLook->productVariantId = $productVariantId;
                $productLook->shopId = $shopId;
                $productLook->remoteShopId=$remoteShopId;
                $productLook->insert();
                $res = \Monkey::app()->dbAdapter->query('select max(id) as id from ProductHasProductLook',[])->fetchAll();
                foreach ($res as $result) {
                    $lastId = $result['id'];
                }
                $findShopId = \Monkey::app()->repoFactory->create('Shop')->findOneBy(['id' => $remoteShopId]);
                $db_host = $findShopId->dbHost;
                $db_name = $findShopId->dbName;
                $db_user = $findShopId->dbUsername;
                $db_pass = $findShopId->dbPassword;
                try {
                    $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
                    $db_con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                    $res = " connessione ok <br>";
                } catch (PDOException $e) {
                    throw new BambooException('fail to connect');

                }
                $stmtRemoteInsertPc = $db_con->prepare("INSERT INTO ProductHasProductLook (`productLookId`,`productId`,productVariantId,`shopId`) VALUES (
        '" . $remoteLookId . "',
        '" . $productId . "',
        '" . $productVariantId . "',
        '" . $shopId . "'
        )");

                $stmtRemoteInsertPc->execute();
                $remoteId = $db_con->lastInsertId();
                $updatePc = $productHasProductLookRepo->findOneBy(['id' => $lastId]);
                $updatePc->remoteId = $remoteId;
                $updatePc->update();
                $res .= 'inserito prodotto ' . $productId . '-' . $productVariantId . '  su look ' . $code . '</br>';
            } else {
                $res .= 'prodotto  ' . $productId . '-' . $productVariantId . ' esistente non inserito su look ' . $code.' </br>';
                continue;
            }
        }


        return $res;

    }

    public function put()
    {

    }

    public function delete()
    {

    }
}