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
use bamboo\domain\entities\CNewsletterUser;
use bamboo\domain\repositories\CNewsletterRepo;
use bamboo\domain\repositories\CNewsletterUserRepo;

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
  ud.surname


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