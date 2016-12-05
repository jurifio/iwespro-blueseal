<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;


/**
 * Class CProductDetailListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CNewsletterEmailListAjaxController extends AAjaxController
{
    public function get()
    {
        $sql = "SELECT n.id, if(n.isActive = 1,'Attiva','Non Attiva') , l.name as lang, ud.name, ud.surname, n.subscriptionDate FROM 
                        Newsletter n 
                        JOIN Lang l ON n.langId = l.id 
                        LEFT JOIN (User u 
                          JOIN UserDetails ud ON u.id = ud.userId) 
                        ON n.userId = u.id ";
        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $newsletter = $this->app->repoFactory->create('Newsletter')->em()->findBySql($datatable->getQuery(), $datatable->getParams());
        $count = $this->app->repoFactory->create('Newsletter')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->app->repoFactory->create('Newsletter')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());


        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        foreach ($newsletter as $val) {
            $row = [];
            $user = $val->user;
            $row["DT_RowId"] = $val->id;
            $row["DT_RowClass"] = 'colore';
            $row['email'] = $val->email;
            $row['name'] = ($user) ? $user->name : '-';
            $row['surname'] = ($user) ? $user->surname : '-';
            $row['isActive'] = ($val->isActive) ? "Attiva" : "Non Attiva";
            $row['subscriptionDate'] = ($val->subscriptionDate) ? $val->subscriptionDate : "-";
            $row['unsubscriptionDate'] = ($val->unsubscriptionDate) ? $val->unsubscriptionDate : "-";
            $row['lang'] = $val->lang->lang;
            $response['data'][] = $row;
        }

        return json_encode($response);
    }
}