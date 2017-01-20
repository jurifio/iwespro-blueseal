<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;

/**
 * Class CProductListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CUserListAjaxController extends AAjaxController
{
    public function get()
    {
        $sql = "SELECT
                      `u`.`id`                                            AS `id`,
                      `ud`.`name`                                         AS `name`,
                      `ud`.`surname`                                      AS `surname`,
                      `u`.`email`                                         AS `email`,
                      if((`ud`.`gender` = 'F'), 'Donna', 'Uomo')          AS `sex`,
                      if((`u`.`isActive` = 1), 'Attivato', 'Disattivato') AS `status`,
                      `u`.`creationDate`                                  AS `creationDate`,
                      ud.note as notes,
                      ud.phone
                    FROM (`User` `u`
                      JOIN `UserDetails` `ud`)
                    WHERE ((`u`.`id` = `ud`.`userId`) AND (`u`.`isDeleted` = 0))
                    ORDER BY `u`.`creationDate` DESC";
        $datatable = new CDataTables($sql,['id'],$_GET,true);

        $users = $this->app->repoFactory->create('User')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->app->repoFactory->create('User')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->app->repoFactory->create('User')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $userEdit = $this->app->baseUrl(false). "/blueseal/utente?userId=";

        foreach($users as $val)
        {
            $row = [];
            $row["DT_RowId"] = 'row__'.$val->printId();
            $row["DT_RowClass"] = $val->isActive == 1 ? 'active' : 'unactive' ;;
            $row['id'] = $val->id;// '<a href="'.$userEdit.$val->id.'">'.$val->id.'</a>';
            $row['name'] = $val->userDetails->name;
            $row['surname'] = $val->userDetails->surname;
            $row['email'] = $val->email;
            $row['notes'] = $val->userDetails->note;
            $row['method'] = $val->registrationEntryPoint;
            $row['sex'] = $val->userDetails->gender == 'M' ? 'Uomo' : 'Donna';
            $color = $val->isActive == 1 ? '#008200' : '';
            $icon = "fa-user";
            if(isset($val->rbacRole) && !$val->rbacRole->isEmpty() ){
                $color =  "#cbac59";
                if($val->rbacRole->findOneByKey('title','sa')){
                    $icon = "fa-user-secret";
                }
            }
            $row['status'] = '<i style="color: '.$color.'" class="fa '.$icon.'"></i>';
            $row['phone'] = $val->userDetails->phone;
            $row['creationDate'] = $val->creationDate;
            $response[] = $row;
        }

        return json_encode($response);
    }
}