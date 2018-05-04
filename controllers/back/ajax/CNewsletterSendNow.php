<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CNewsletterCampaign;
use bamboo\domain\repositories\CNewsletterCampaignRepo;
use bamboo\domain\entities\COrder;
use bamboo\core\jobs\ACronJob;
use bamboo\domain\repositories\CNewsletterRepo;


/**
 * Class CProductSizeGroupManage
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
class CNewsletterSendNow extends AAjaxController
{



    /**
     * @return mixed
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */

    public function put(){
        $data  = $this->app->router->request()->getRequestData();
        $id = $data["id"];
        $finalpositionId = strpos($id,'</a>');
        $initialpositionId =strpos($id,'">');
        $finalpositionId=$finalpositionId;
        $initialpositionId=$initialpositionId+2;
        $lenghtposition =$finalpositionId-$initialpositionId;
        $id=substr($id, $initialpositionId,$lenghtposition);

        $sql = "Select * from Newsletter where id='".$id."'";
        /** @var CNewsletterRepo $newslettersRepo */
        $newslettersRepo = \Monkey::app()->repoFactory->create('Newsletter');

        $newsletters = $newslettersRepo->findBySql($sql);

        if(empty($newsletters)) return;
       $res='Starting'.' Newsletters to send: '.count($newsletters);
        foreach ($newsletters as $newsletter) {
             $newslettersRepo->sendNewsletterEmails($newsletter, ENV !== 'prod',true);

        }

        /** @var CRepo $newsletterUserRepo */
        $newsletterUserRepo = \Monkey::app()->repoFactory->create('Newsletter');

        /** @var CNewsletter $newsletter */
        $newsletter = $newsletterUserRepo->findOneBy(['id' => $id]);
        $now =  (new \DateTime())->format('Y-m-d H:i:s');
        $newsletter->sendAddressDate = $now;
        $newsletter->update();
        return $res;


    }



}