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
class CProductHasProductCorrelationManageAjaxController extends AAjaxController
{
    public function get()
    {
        $correlation = [];
        $shopRepo=\Monkey::app()->repoFactory->create('Shop');
        $productCorrelation = \Monkey::app()->repoFactory->create('ProductCorrelation')->findAll();
        foreach ($productCorrelation as $collect) {
            $shop=$shopRepo->findOneBy(['id'=>$collect->remoteShopId]);
            if ($collect->image != null) {
                $image = $collect->image;
            } else {
                $image = '';
            }
            $correlation[] = ['id' => $collect->id,'name' => $collect->name,'code' => $collect->code,'img' => $image,'shopId' => $shop->id,'shopName' => $shop->name];
        }

        return json_encode($correlation);

    }

    public function post()
    {
        $res = '';
        $data = \Monkey::app()->router->request()->getRequestData();
        $shopHasProductRepo = \Monkey::app()->repoFactory->create('ShopHasProduct');
        $productHasProductCorrelation = \Monkey::app()->repoFactory->create('ProductHasProductCorrelation');
        $productCorrelationRepo = \Monkey::app()->repoFactory->create('ProductCorrelation');
        $code = $data['code'];
        $products = $data['row'];
        foreach ($products as $product) {
            $prod = explode('-',$product);
            $productId = $prod[0];
            $productVariantId = $prod[1];
            $shopId = $shopHasProductRepo->findOneBy(['productId' => $productId,'productVariantId' => $productVariantId])->shopId;
            $pl=\Monkey::app()->repoFactory->create('ProductCorrelation')->findOneBy(['id'=>$code]);
            $remoteCorrelationId=$pl->remoteId;
            $remoteShopId=$pl->remoteShopId;
            $findProduct = $productHasProductCorrelation->findOneBy(['correlationId' => $code,'productId' => $productId,'productVariantId' => $productVariantId,'shopId' => $shopId]);
            if ($findProduct == null) {
                $productCorrel = $productHasProductCorrelation->getEmptyEntity();
                $productCorrel->correlationId = $code;
                $productCorrel->productId = $productId;
                $productCorrel->productVariantId = $productVariantId;
                $productCorrel->shopId = $shopId;
                $productCorrel->remoteShopId = $remoteShopId;
                $productCorrel->insert();
                $res = \Monkey::app()->dbAdapter->query('select max(id) as id from ProductHasProductCorrelation',[])->fetchAll();
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
                $stmtRemoteInsertPc = $db_con->prepare("INSERT INTO ProductHasProductCorrelation (`correlationId`,`productId`,productVariantId,`shopId`) VALUES (
        '" . $remoteCorrelationId . "',
        '" . $productId . "',
        '" . $productVariantId . "',
        '" . $shopId . "'
        )");

                $stmtRemoteInsertPc->execute();
                $remoteId = $db_con->lastInsertId();
                $updatePc = $productHasProductCorrelation->findOneBy(['id' => $lastId]);
                $updatePc->remoteId = $remoteId;
                $updatePc->update();
                $res .= 'inserito prodotto ' . $productId . '-' . $productVariantId . '  su correlazione ' . $code . '</br>';
            } else {
                $res .= 'prodotto  ' . $productId . '-' . $productVariantId . ' esistente non inserito su ' . $code.' </br>';
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