<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\exceptions\BambooException;
use bamboo\core\intl\CLang;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CProductHasProductCorrelation;
use PDO;
USE PDOException;



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
class CProductCorrelationAjaxController extends AAjaxController
{
    public function post()
    {
        try {
            $data = \Monkey::app()->router->request()->getRequestData();
            $productCorrelationRepo = \Monkey::app()->repoFactory->create('ProductCorrelation');
            $name = $data['name'];
            $code = $data['code'];
           if(($data['shopId']==null) || ($data['shopId']=='')){
               return 'Devi Selezionare lo Shop di Destinazione';
            }else{
                $shopId = $data['shopId'];
            }
            $image = $data['image'];
            $seo = $data['seo'];
            if ($name == null) {
                return 'Nome non Valorizzato';
            }
            $description = $data['description'];
            $note = $data['note'];
            $findpc = $productCorrelationRepo->findOneBy(['name' => $name]);
            if ($findpc != null) {
                return 'Esiste Già una correlazione con questo nome';
            }
            $pc = $productCorrelationRepo->getEmptyEntity();
            $pc->name = $name;
            $pc->description = $description;
            $pc->note = $note;
            $pc->code = $code;
            $pc->seo = $seo;
            $pc->remoteShopId = $shopId;
            $pc->image = $image;
            $pc->insert();
            $res = \Monkey::app()->dbAdapter->query('select max(id) as id from ProductCorrelation ',[])->fetchAll();
            foreach ($res as $result) {
                $lastId = $result['id'];
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
            $stmtRemoteInsertPc = $db_con->prepare("INSERT INTO ProductCorrelation (`name`,`description`,note,`code`,seo,`image`) VALUES (
        '" . $name . "',
        '" . $description . "',
        '" . $note . "',
        '" . $code . "',
        '" . $seo . "',
        '" . $image . "'
        )");

            $stmtRemoteInsertPc->execute();
            $remoteId = $db_con->lastInsertId();
            $updatePc = $productCorrelationRepo->findOneBy(['id' => $lastId]);
            $updatePc->remoteId = $remoteId;
            $updatePc->update();
            return 'Correlazione inserita con successo';
        }catch (\Throwable $e){
            \Monkey::app()->applicationLog('CProductCorrelationAjaxController','Error','Insert Correlation',$e->getMessage(),$e->getLine());
            return 'Errore TraceLog '.$e->getMessage();
    }





    }
    public function put()
    {
        try {
            $data = \Monkey::app()->router->request()->getRequestData();
            $productCorrelationRepo = \Monkey::app()->repoFactory->create('ProductCorrelation');
            $id = $data['id'];
            $name = $data['name'];
            $code = $data['code'];
            $seo = $data['seo'];
            $shopId = $data['shopId'];
            $remoteId = $data['remoteId'];
            if ($name == null) {
                return 'Nome non Valorizzato';
            }

            $description = $data['description'];
            $note = $data['note'];
            $pc = $productCorrelationRepo->findOneBy(['id' => $id]);
            $pc->name = $name;
            $pc->description = $description;
            $pc->note = $note;
            $pc->code = $code;
            $pc->seo = $seo;
            $pc->update();
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
            $stmtUpdatePc = $db_con->prepare("UPDATE ProductCorrelation set
                              `name`='" . $name . "',
                              `description`='" . $description . "',
                               note='" . $note . "',
                               `code`='" . $code . "',
                               `seo`='" . $seo . "'
                                where id=" . $remoteId);
            $stmtUpdatePc->execute();
            return 'Correlazione Aggiornata con successo';
        }catch (\Throwable $e){
            \Monkey::app()->applicationLog('CProductCorrelationAjaxController','error','Update ProductCorrelation',$e->getMessage(),$e->getLine());
            return 'Error TraceLog '.$e->getMessage();
        }
    }
    public function delete()
    {
        try {
            $data = \Monkey::app()->router->request()->getRequestData();
            /** @var CRepo $productCorrelationRepo */
            $productCorrelationRepo = \Monkey::app()->repoFactory->create('ProductCorrelation');
            /** @var CRepo $productHasProductCorrelationRepo */
            $productHasProductCorrelationRepo = \Monkey::app()->repoFactory->create('ProductHasProductCorrelation');
            $id = $data['id'];
            $shopId = $data['remoteShopId'];
            $remoteId = $data['remoteId'];
            /** @var CProductHasProductCorrelation $phpc */
            $phpc = $productHasProductCorrelationRepo->findBy(['correlationId' => $id]);
            foreach ($phpc as $values) {
                $values->delete();
            }
            /** @var CProductCorrelation $pc */
            $pc = $productCorrelationRepo->findOneBy(['id' => $id]);
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
            $stmtDeletePc = $db_con->prepare("DELETE FROM ProductCorrelation 
                                where id=" . $remoteId);
            $stmtDeletePc->execute();
            $stmtDeletePhpc=$db_con->prepare("DELETE FROM ProductHasProductCorrelation 
                                where correlationId=" . $remoteId);
            $stmtDeletePhpc->execute();

            return 'Correlazioni  Cancellate con successo';
        }catch(\Throwable $e){
            \Monkey::app()->applicationLog('CProductCorrelationAjaxController','Error','Delete Correlation',$e->getMessage(),$e->getLine());
       return 'Error TraceLog '.$e->getMessage();
        }
    }
}