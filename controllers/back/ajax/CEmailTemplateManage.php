<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CEmailTemplate;
use PDO;
use PDOException;


/**
 * Class CEmailTemplateManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 24/12/2019
 * @since 1.0
 */
class CEmailTemplateManage extends AAjaxController
{
    /**
     * @return int
     * @throws BambooException
     * @throws \Exception
     */
    public function post()
    {
        //prendo i dati passati in input
        $data = \Monkey::app()->router->request()->getRequestData();
        $name = $data['name'];
        $template = $data['template'];
        $shopId =$data['shopId'];
        $isActive=$data['isActive'];
        $subject=$data['subject'];
        $scope=$data['scope'];
        $description=$data['description'];
        $arrayTemplate=$data['arraytemplate'];




        /** @var CRepo $emailTemplateRepo */
        $emailTemplateRepo = \Monkey::app()->repoFactory->create('EmailTemplate');
        $emailTemplateTranslationRepo=\Monkey::app()->repoFactory->create('EmailTemplateTranslation');
        $langRepo=\Monkey::app()->repoFactory->create('Lang');

        /** @var CemailTemplate $emailTemplate */
        $emailTemplate = $emailTemplateRepo->findOneBy(['name' => $name,'shopId'=>$shopId]);

        if (empty($emailTemplate)){
            //se la variabile non è istanziata inserisci in db

            /** @var CEmailTemplate $emailTemplateInsert   */
            $emailTemplateInsert = \Monkey::app()->repoFactory->create('EmailTemplate')->getEmptyEntity();
            //popolo la tabella

            $emailTemplateInsert->name = $name ;
            $emailTemplateInsert->template = $template;
            $emailTemplateInsert->subject=$subject;
            $emailTemplateInsert->scope=$scope;
            $emailTemplateInsert->shopId=$shopId;
            $emailTemplateInsert->isActive=$isActive;
            $emailTemplateInsert->description=$description;


            // eseguo la commit sulla tabella;



            //inserimento remoto
            $shopRepo = \Monkey::app()->repoFactory->create('Shop')->findOneBy(['id' => $shopId]);
            $db_host = $shopRepo->dbHost;
            $db_name = $shopRepo->dbName;
            $db_user = $shopRepo->dbUsername;
            $db_pass = $shopRepo->dbPassword;
            try {

                $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
                $db_con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                $res = ' connessione ok <br>';
            } catch (PDOException $e) {
                $res = $e->getMessage();
            }

                $stmtInsertEmailTemplate = $db_con->prepare("INSERT INTO EmailTemplate(`name`,`oldTemplatephp`,`scope`,`description`,`shopId`,`subject`,`isActive`,`template`)
                    VALUES (
                            '".$name."',
                            null,
                            '".$scope."',
                            '".$description."',
                            '".$shopId."',
                            '".$subject."',
                            '".$isActive."',
                            '".$template."'   
                    )");
                $stmtInsertEmailTemplate->execute();
                $remoteId = $db_con -> lastInsertId();
                $emailTemplateInsert->remoteId=$remoteId;
                $emailTemplateInsert->smartInsert();
            
            
            
           $findId= \Monkey::app()->repoFactory->create('EmailTemplate')->findOneBy(['name'=>$name,'shopId'=>$shopId,'scope'=>$scope]);
            foreach($arrayTemplate as $key => $row){
                $emailTemplateTranslation=$emailTemplateTranslationRepo->getEmptyEntity();
                $langTemplate=$row['id'];
                $lang=$langRepo->findOneBy(['lang'=>$langTemplate]);
                $langId=$lang->id;
                $langText=$row['template'];
                $emailTemplateTranslation->langId=$langId;
                $emailTemplateTranslation->emailTemplateId=$findId->id;
                $emailTemplateTranslation->templateTranslation=$langText;
            $stmtInsertEmailTemplateTranslation=$db_con->prepare("INSERT INTO EmailTemplateTranslation (`langId`,`templateEmailId`,`template`) VALUES 
                '".$langId.",
                '".$remoteId.",
                '".$langText.",
                '");
            $stmtInsertEmailTemplateTranslation->execute();
            $lastEmailTemplateTranslationId=$db_con->lastInsertId();
                $emailTemplateTranslation->remoteId=$lastEmailTemplateTranslationId;
                $emailTemplateTranslation->smartInsert();
            }

            $res = "Template inserito con successo!";

        }else{
            //Se hai trovato qualcosa allora restituitsci messaggio di errore
            $res = "Esiste già un file  template  con lo stesso nome";
        }

        return $res;

    }
    public function put()
    {
        //prendo i dati passati in input
        $data = \Monkey::app()->router->request()->getRequestData();
        $name = $data['name'];
        $template = $data['template'];
        $id = $data['id'];
        $shopId =$data['shopId'];
        $isActive=$data['isActive'];
        $subject=$data['subject'];
        $scope=$data['scope'];
        $description=$data['description'];
        $arrayTemplate=$data['arraytemplate'];


        /** @var CRepo $emailTemplateRepo */
        $emailTemplateRepo = \Monkey::app()->repoFactory->create('EmailTemplate');

        $emailTemplateTranslationRepo=\Monkey::app()->repoFactory->create('EmailTemplateTranslation');
        $langRepo=\Monkey::app()->repoFactory->create('Lang');

        /** @var CEmailTemplate $emailTemplate */
        $emailTemplate = $emailTemplateRepo->findOneBy(['id' => $id]);


        if (!empty($emailTemplate)){
            //se la variabile non è istanziata inserisci in db

            $emailTemplate->name = $name ;
            $emailTemplate->template =$template;
            $emailTemplate->subject=$subject;
            $emailTemplate->scope=$scope;
            $emailTemplate->shopId=$shopId;
            $emailTemplate->isActive=$isActive;
            $emailTemplate->description=$description;
            $shopRepo = \Monkey::app()->repoFactory->create('Shop')->findOneBy(['id' => $shopId]);
            $db_host = $shopRepo->dbHost;
            $db_name = $shopRepo->dbName;
            $db_user = $shopRepo->dbUsername;
            $db_pass = $shopRepo->dbPassword;
            try {

                $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
                $db_con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                $res = ' connessione ok <br>';
            } catch (PDOException $e) {
                $res = $e->getMessage();
            }
            $stmtUpdateEmailTemplate=$db_con->prepare("UPDATE EmailTemplate SET 
                                                                `name`='".$name."',
                                                                `oldTemplatephp`=null,
                                                                 `scope`='".$scope."',
                                                                 `description`='".$description."',
                                                                 `shopId`='".$shopId."',
                                                                 `subject`='".$subject."',
                                                                 `isActive`='".$isActive."',
                                                                 `template`='".$template."'
                                                                  where id=".$emailTemplate->remoteId);
            $stmtUpdateEmailTemplate->execute();
            // eseguo la commit sulla tabella;
            $emailTemplate->update();

            foreach($arrayTemplate as $key => $row) {
                $emailTemplateTranslation = $emailTemplateTranslationRepo->findOneBy(['id' => $row['id']]);
                $langText = $row['template'];
                $emailTemplateTranslation->templateTranslation = $langText;
                $stmtUpdateEmailTemplateTranslation=$db_con->prepare("UPDATE EmailTemplateTranslation SET template='".$langText."' WHERE id=".$emailTemplateTranslation->remoteId);
                $stmtUpdateEmailTemplateTranslation->execute();
                $emailTemplateTranslation->update();
            }
            $res = "Template Modifcato con successo!";

        }else{
            //Se hai trovato qualcosa allora restituitsci messaggio di errore
            $res = "non è stato trovato il template";
        }

        return $res;



    }



}