<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CUser;
use bamboo\domain\repositories\CUserRepo;

/**
 * Class CProductListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>, ${DATE}
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
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
                      `pl`.`name` AS priceListName,
                      `pl`.`id` AS priceListId,
                      `u`.remoteshopId as shopId,  
                      `l`.`name` as langName,  
                      if((`ud`.`gender` = 'F'), 'Donna', 'Uomo')          AS `sex`,
                      if((`u`.`isActive` = 1), 'Attivato', 'Disattivato') AS `status`,
                      `u`.`creationDate`                                  AS `creationDate`,
                      ud.note,
                      ud.phone
                    FROM `User` `u`
                      JOIN `UserDetails` `ud`
                   on ((`u`.`id` = `ud`.`userId`) AND (`u`.`isDeleted` = 0))
                        join PriceList pl on u.priceListId=pl.id and u.remoteShopId=pl.shopId 
                        join Lang l on u.langId=l.id 
                    ORDER BY `u`.`creationDate` DESC";
        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings();

        $userEdit = $this->app->baseUrl(false) . "/blueseal/utente?userId=";

        $priceListRepo=\Monkey::app()->repoFactory->create('PriceList');
        $langRepo=\Monkey::app()->repoFactory->create('Lang');
        /** @var CUserRepo $userRepo */
        $userRepo = \Monkey::app()->repoFactory->create('User');
        foreach ($datatable->getResponseSetData() as $key => $row) {
            /** @var CUser $user */
            $user = $userRepo->findOne([$row['id']]);
            $row = [];
            $row["DT_RowId"] = 'row__' . $user->printId();
            $row["DT_RowClass"] = $user->isActive == 1 ? 'active' : 'unactive';;
            $row['id'] = '<a href="'.$userEdit.$user->id.'">'.$user->id.'</a>';
            $row['name'] = $user->userDetails->name;
            $row['surname'] = $user->userDetails->surname;
            $priceList=$priceListRepo->findOneBy(['id'=>$user->priceListId,'shopId'=>$user->remoteShopId]);
            $lang=$langRepo->findOneBy(['id'=>$user->langId]);
            $row['email'] = $user->email;
            $row['note'] = $user->userDetails->note;
            $row['method'] = $user->registrationEntryPoint;
            $row['priceListName']=$priceList->name;
            $row['priceListId']=$user->priceListId;
            $row['remoteShopId']=$user->remoteShopId;
            $row['langName']=$lang->name;
            $row['sex'] = $user->userDetails->gender == 'M' ? 'Uomo' : 'Donna';
            $color = $user->isActive == 1 ? '#008200' : '';
            $icon = "fa-user";
            if (isset($user->rbacRole) && !$user->rbacRole->isEmpty()) {
                $color = "#cbac59";
                if ($user->rbacRole->findOneByKey('title', 'sa')) {
                    $icon = "fa-user-secret";
                }
            }
            $row['status'] = '<i style="color: ' . $color . '" class="fa ' . $icon . '"></i>';
            $row['phone'] = $user->userDetails->phone;
            $row['creationDate'] = $user->creationDate;
            $datatable->setResponseDataSetRow($key, $row);
        }

        return $datatable->responseOut();
    }
}