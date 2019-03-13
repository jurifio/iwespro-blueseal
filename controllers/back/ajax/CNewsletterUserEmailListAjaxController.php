<?php
/**
 * Created by PhpStorm.
 * User: jurif
 * Date: 08/01/2018
 * Time: 16:55
 */

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CNewsletter;
use bamboo\domain\entities\CNewsletterEmailList;
use bamboo\domain\entities\CNewsletterUser;
use bamboo\domain\repositories\CNewsletterRepo;
use bamboo\domain\repositories\CNewsletterUserRepo;
use bamboo\core\base\CObjectCollection;

class CNewsletterUserEmailListAjaxController extends AAjaxController
{

    public function get()
    {
        $sql = "SELECT
                  n.id,
                  n.email,
                  n.isActive as isActive ,
                  n.subscriptionDate,
                  n.unsubscriptionDate,
                  ud.name,
                  ud.surname,
                  ud.gender,
                  n.genderNewsletterUser as nuG
                  FROM NewsletterUser n 
                  LEFT JOIN UserDetails ud ON n.userId = ud.userId";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings();



        /** @var CNewsletterUserRepo $newsletterUserRepo */
        $newsletterUserRepo = \Monkey::app()->repoFactory->create('NewsletterUser');

        foreach ($datatable->getResponseSetData() as $key=>$row) {
            /** @var CNewsletterUser $newsletterUser */
            $newsletterUser = $newsletterUserRepo->findOneBy(['id' => $row['id']]);

            $row['id'] = $newsletterUser->id;
            $row['email'] = $newsletterUser->email;
            $row['name'] = is_null($newsletterUser->userId) ? $newsletterUser->nameNewsletter : $newsletterUser->user->userDetails->name;
            $row['surname'] = is_null($newsletterUser->userId) ? $newsletterUser->surnameNewsletter : $newsletterUser->user->userDetails->surname;
            $row['subscriptionDate'] = $newsletterUser->subscriptionDate;
            $row['unsubscriptionDate'] = $newsletterUser->unsubscriptionDate;
            $row['isActive'] = $newsletterUser->isActive ? 'Attivo' : 'Non attivo';
            $row['gender'] = is_null($newsletterUser->userId) ? $newsletterUser->genderNewsletterUser : $newsletterUser->user->userDetails->gender;


            $datatable->setResponseDataSetRow($key,$row);
        }
        return $datatable->responseOut();

    }
}