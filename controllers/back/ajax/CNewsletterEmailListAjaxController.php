<?php
namespace bamboo\controllers\back\ajax;

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
        $sql = "SELECT n.id, if(n.isActive = 1,'Attiva','Non Attiva') as isActive  , l.name as lang, ud.name, ud.surname, n.subscriptionDate FROM 
                        NewsletterUser n 
                        JOIN Lang l ON n.langId = l.id 
                        LEFT JOIN (User u 
                          JOIN UserDetails ud ON u.id = ud.userId) 
                        ON n.userId = u.id ";
        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings();

        foreach ($datatable->getResponseSetData() as $key=>$row) {
            $val = \Monkey::app()->repoFactory->create('NewsletterUser')->findOne([$row['id']]);

            $user = $val->user;
            $row["DT_RowId"] = $val->id;
            $row["DT_RowClass"] = 'colore';
            $row['email'] = $val->email;
            try {
                $row['name'] = ($user) ? $user->name : '-';
                $row['surname'] = ($user) ? $user->surname : '-';
            }catch (\Throwable $e) {
                //check this
            }
            $row['isActive'] = ($val->isActive) ? "Attiva" : "Non Attiva";
            $row['subscriptionDate'] = ($val->subscriptionDate) ? $val->subscriptionDate : "-";
            $row['unsubscriptionDate'] = ($val->unsubscriptionDate) ? $val->unsubscriptionDate : "-";
            $row['lang'] = $val->lang->lang;
            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();
    }
}