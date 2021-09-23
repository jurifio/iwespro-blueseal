<?php
namespace bamboo\controllers\back\ajax;
use bamboo\core\exceptions\BambooException;
use PDO;
USE PDOException;
use Throwable;
/**
 * Class CPriceListAssignAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 10/09/2021
 * @since 1.0
 */
class CPriceListAssignAjaxController extends AAjaxController
{
    public function put()
    {
        try {
            $data = $this->app->router->request()->getRequestData();
            $userId = $data['userId'];
            $shopId = $data['shopId'];
            $priceListId = $data['priceListId'];
            $user = \Monkey::app()->repoFactory->create('User')->findOneBy(['id' => $userId,'remoteShopId' => $shopId]);
            $remoteId = $user->remoteId;
            $user->priceListId = $priceListId;
            $user->update();
            $findShopId = \Monkey::app()->repoFactory->create('Shop')->findOneBy(['id' => $shopId]);
            if(!is_null($remoteId)) {
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
                $stmtUpdatePriceList = $db_con->prepare("UPDATE `User` set priceListId='.$priceListId.'  where userId=" . $remoteId . " and remoteShopId=" . $shopId);
                $stmtUpdatePriceList->execute();
            }
            return 'Assegnazione Listino Eseguito';
        }catch(\Throwable $e){
            return $e;
        }


    }
}