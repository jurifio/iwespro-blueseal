<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CNewsletterEmailList;
use bamboo\domain\repositories\CNewsletterEmailListRepo;


/**
 * Class CNewsletterEmailList
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CNewsletterEmailListManage extends AAjaxController
{
    /**
     * @return int
     * @throws BambooException
     * @throws \Exception
     */
    public function get()
    {
        $sql = "select u.id, u.name, u.sql  from pickyshop_dev.NewsletterEmailList u";
        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings('true');

        foreach ($datatable->getResponseSetData() as $key=>$row) {

        }

        return $datatable->responseOut();
    }

    public function post()
    {
        //prendo i dati passati in input
        $data = \Monkey::app()->router->request()->getRequestData();
        $name = $data['name'];
        $sql    = $data['sql'];
        $newsletterEmailListId = $data['newsletterEmailListId'];
        if( empty($sql)){
            $sql="vuoto";
        }

        if(empty($name) || empty($sql) || empty($newsletterEmailListId)){
            $res = "prego compila tutti i campi premi Annulla e compila i campi mancanti<br>Nome Lista Destinatari".$name."<br>Filtro  sql Generato:".$sql."<br>Id Codice newletter Associata:".$newsletterEmailListId;

        } else {


            /** @var CRepo $newsletterEmailListRepo */
            $newsletterEmailListRepo = \Monkey::app()->repoFactory->create('NewsletterEmailList');

            /** @var CNewsletterEmailList $newsletterEmailList */
            $newsletterEmailList = $newsletterEmailListRepo->findOneBy(['name' => $name]);


            if (empty($newsletterEmailList)) {
                //se la variabile non è istanziata inserisci in db

                /** @var CNewsletterEmailList $newsletterEmailListInsert */
                $newsletterEmailListInsert = \Monkey::app()->repoFactory->create('NewsletterEmailList')->getEmptyEntity();
                //popolo la tabella

                $newsletterEmailListInsert->name = $name;
                $newsletterEmailListInsert->sql = $sql;
                $newsletterEmailListInsert->newsletterEmailListId = $newsletterEmailListId;

                // eseguo la commit sulla tabella;

                $newsletterEmailListInsert->smartInsert();

                $res = "filtro Lista Destinatari inserito con successo!";

            } else {
                //Se hai trovato qualcosa allora restituitsci messaggio di errore
                $res = "Esiste già un filtro lista Destinatari con lo stesso nome";
            }
        }

        return $res;












    }



}