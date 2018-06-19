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
                  n.gender as nuG,
                  ud.gender
FROM NewsletterUser n left outer join UserEmail ue On n.userId =ue.userId
left outer join UserDetails ud ON n.userId=ud.userId";
        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings(true);



        /** @var CNewsletterRepo $newsletterRepo */
        $newsletterRepo = \Monkey::app()->repoFactory->create('NewsletterUser');




        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var CNewsletter $newsletter */
            $newsletter = $newsletterRepo->findOneBy(['id' => $row["id"] ]);
            $verified=$newsletter->isActive;
            $email=$newsletter->email;
            /** @var CRepo $newsletterEmailList */
            $newsletterEmailList=\Monkey::app()->repoFactory->create('NewsletterGroup');
            $group=[];

            /** @var CObjectCollection $newslettersql */
            $newslettersql = $newsletterEmailList->findAll();
            foreach ($newslettersql as $value) {

                $sql = $value->sql . " and nu.email LIKE '%" . $email . "%' GROUP by nu.email";

                $searchRes = $this->app->dbAdapter->query($sql,array())->fetchAll();
                foreach ($searchRes as $val) {


                    if ($email == $val['email']) {
                        $news=$value->name;
                    array_push($group,$news);

                        //$group =$value->name;
                    }

                }

            }
            $row['List']= $group;
            if ($verified=="1") {

                $row['isActive'] = "Attivo";
            }else{
                $row['isActive']="Non Attivo";
            }
            $datatable->setResponseDataSetRow($key,$row);
        }
        return $datatable->responseOut();

    }
}