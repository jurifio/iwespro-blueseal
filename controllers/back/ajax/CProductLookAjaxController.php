<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\exceptions\BambooException;
use bamboo\core\intl\CLang;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CProductHasProductCorrelation;
use PDO;
use PDOException;
use Throwable;


/**
 * Class CProductCorrelationAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 13/06/2020
 * @since 1.0
 */
class CProductLookAjaxController extends AAjaxController
{
    public function post()
    {
        $data = \Monkey::app()->router->request()->getRequestData();
        $productLookRepo = \Monkey::app()->repoFactory->create('ProductLook');
        $name = $data['name'];
        $images = $data['image'];
        $discountActive = $data['discountActive'];
        $typeDiscount = $data['typeDiscount'];
        $amount = $data['amount'];
        if ($name == null) {
            return 'Nome non Valorizzato';
        }
        if (($data['shopId'] == null) || ($data['shopId'] == '')) {
            return 'Devi Selezionare lo Shop di Destinazione';
        } else {
            $shopId = $data['shopId'];
        }
        $description = $data['description'];
        $note = $data['note'];
        $findpc = $productLookRepo->findOneBy(['name' => $name]);
        if ($findpc != null) {
            return 'Esiste Già una Look con questo nome';
        }
        $pc = $productLookRepo->getEmptyEntity();
        $i = 0;
        foreach ($images as $image) {
            if ($i == 0) {
                $pc->image = $image;

            } elseif ($i == 1) {
                $pc->image2 = $image;

            } elseif ($i == 2) {
                $pc->image3 = $image;

            } elseif ($i == 4) {
                $pc->image4 = $image;

            } elseif ($i == 5) {
                $pc->video1 = $image;

            } elseif ($i == 7) {
                $pc->video2 = $image;

            } elseif ($i == 8) {
                $pc->video3 = $image;

            } elseif ($i == 9) {
                $pc->video4 = $image;

            } else {
                break;
            }

            $i++;
        }
        $pc->name = $name;
        $pc->discountActive = $discountActive;
        $pc->typeDiscount = $typeDiscount;
        $pc->amount = $amount;
        $pc->description = $description;
        $pc->note = $note;
        $pc->remoteShopId = $shopId;
        $pc->image = $image;
        $pc->insert();
        $res = \Monkey::app()->dbAdapter->query('select max(id) as id,image,image2,image3,image4,video1,video2,video3,video4 from ProductLook ',[])->fetchAll();
        foreach ($res as $result) {
            $lastId = $result['id'];
            $image = $result['image'];
            $image2 = $result['image2'];
            $image3 = $result['image3'];
            $image4 = $result['image4'];
            $video1 = $result['video1'];
            $video2 = $result['video2'];
            $video3 = $result['video3'];
            $video4 = $result['video4'];
        }
        $findShopId = \Monkey::app()->repoFactory->create('Shop')->findOneBy(['id' => $shopId]);
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
        $stmtRemoteInsertPl = $db_con->prepare("INSERT INTO ProductLook (`name`,`description`,note,`image`,image2,`image3`,`image4`,`video1`,`video2`,`video3`,`video4`,`discountActive`,`typeDiscount`,`amount`) VALUES (
        '" . $name . "',
        '" . $description . "',
        '" . $note . "',
        '" . $image . "',
        '" . $image2 . "',
        '" . $image3 . "',
        '" . $image4 . "',
        '" . $video1 . "',
        '" . $video2 . "',  
        '" . $video3 . "',
        '" . $video4 . "',
        '" . $discountActive . "',
        '" . $typeDiscount . "',
        '" . $amount . "'
        )");

        $stmtRemoteInsertPl->execute();
        $remoteId = $db_con->lastInsertId();
        $updatePl = $productLookRepo->findOneBy(['id' => $lastId]);
        $updatePl->remoteId = $remoteId;
        $updatePl->update();

        return 'Look inserito con successo';

    }

    public function put()
    {
        try {
            $data = \Monkey::app()->router->request()->getRequestData();
            $productLookRepo = \Monkey::app()->repoFactory->create('ProductLook');
            $id = $data['id'];
            $remoteId = $data['remoteId'];
            $remoteShopId = $data['remoteShopId'];
            $name = $data['name'];
            $discountActive = $data['discountActive'];
            $typeDiscount = $data['typeDiscount'];
            $amount = $data['amount'];
            if ($name == null) {
                return 'Nome non Valorizzato';
            }
            $description = $data['description'];
            $note = $data['note'];
            $pc = $productLookRepo->findOneBy(['id' => $id]);
            $pc->name = $name;
            $pc->description = $description;
            $pc->note = $note;
            $pc->discountActive = $discountActive;
            $pc->typeDiscount = $typeDiscount;
            $pc->amount = $amount;
            $pc->update();
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
            $stmtRemoteUpdatePl = $db_con->prepare("Update  ProductLook set 
        `name`= '" . $name . "',
        `description`='" . $description . "',
        `note`='" . $note . "',
       `discountActive`='" . $discountActive . "',
       `typeDiscount`='" . $typeDiscount . "',
        `amount`='" . $amount . "' where id=" . $remoteId);
            $stmtRemoteUpdatePl->execute();

            return 'Look Aggiornato con successo';
        } catch (\Throwable $e) {
            \Monkey::app()->applicationLog('CProductLookAjaxController','error','Update ProductLook',$e->getMessage(),$e->getLine());
            return 'Error TraceLog ' . $e->getMessage();
        }
    }

    public function delete()
    {
        try {
            $data = \Monkey::app()->router->request()->getRequestData();
            /** @var CRepo $productLookRepo */
            $productLookRepo = \Monkey::app()->repoFactory->create('ProductLook');
            /** @var CRepo $productHasProductLookRepo */
            $productHasProductLookRepo = \Monkey::app()->repoFactory->create('ProductHasProductLook');
            $id = $data['id'];
            $shopId = $data['remoteShopId'];
            $remoteId = $data['remoteId'];
            /** @var CProductHasProductLook $phpc */
            $phpc = $productHasProductLookRepo->findBy(['productLookId' => $id]);
            if ($phpc) {
                foreach ($phpc as $values) {
                    $values->delete();
                }
            }
            /** @var CProductLook $pc */
            $pc = $productLookRepo->findOneBy(['id' => $id]);
            $pc->delete();
            $findShopId = \Monkey::app()->repoFactory->create('Shop')->findOneBy(['id' => $shopId]);
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
            $stmtDeletePc = $db_con->prepare("DELETE FROM ProductLook 
                                where id=" . $remoteId);
            $stmtDeletePc->execute();
            $stmtDeletePhpc = $db_con->prepare("DELETE FROM ProductLook 
                                where productLookId=" . $remoteId);
            $stmtDeletePhpc->execute();

            return 'Look  Cancellato con successo';
        } catch (\Throwable $e) {
            \Monkey::app()->applicationLog('CProductLookAjaxController','Error','Delete Look',$e->getMessage(),$e->getLine());
            return 'Error TraceLog ' . $e->getMessage();
        }
    }
}