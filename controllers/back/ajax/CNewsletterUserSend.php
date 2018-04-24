<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\email\CEmail;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CEmailRecipient;
use bamboo\domain\entities\CNewsletter;
use bamboo\domain\entities\CUserAddress;
use bamboo\domain\entitiesCNewsletterUser;
use bamboo\domain\entities\CNewsletterCampaign;
use bamboo\domain\entities\CNewsletterEmailList;
use bamboo\domain\entities\CNewsletterEvent;
use bamboo\domain\entities\CNewsletterGroup;
use bamboo\domain\entities\CShipment;
use bamboo\domain\repositories\CEmailRecipientRepo;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\utils\time\STimeToolbox;
use bamboo\domain\entities\CEmailAddress;

/**
 * Class CNewsletterUserSend
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
class CNewsletterUserSend extends AAjaxController
{

    public function secToTime($init){
        $hours = floor($init / 3600);
        $minutes = floor(($init / 60) % 60);
        $seconds = $init % 60;

        return "$hours:$minutes:$seconds";
    }

    public function post()
    {
        $data = \Monkey::app()->router->request()->getRequestData();
        $idNewsletterUser = $data['idNewsletterUser'];

        /** @var CNewsletter $newsletterUser */
        $newsletterUser = \Monkey::app()->repoFactory->create('Newsletter')->findOneBy(['id'=>$idNewsletterUser]);

        $checkNewsletterUser = $newsletterUser;

        if (empty($checkNewsletterUser)) {
            $res = "<p style='color:red'>la Newsletter  che stai cercando di inviare non esiste</p>";

        } else if (!$newsletterUser->id->isEmpty()) {
            // ottengo i valori dalla tabella newsletter

            $fromEmailAddressId    = $newsletterUser->fromEmailAddressId;
            $sendAddressDate       = $newsletterUser->sendAddressDate;
            $newsletterEmailListId = $newsletterUser->newsletterEmailListId;
            $subject               = $newsletterUser->subject;
            $dataDescription       = $newsletterUser->dataDescription;
            $preCompiledTemplate   = $newsletterUser->preCompiledTemplate;
            $newsletterCampaignId  = $newsletterUser->newsletterCampaingId;
            $newsletterEventId     = $newsletterUser->newsletterEventId;


          //  ottengo le informazioni del sender;

            /** @var CEmailAddress $emailAddress */

            $emailAddress = \Monkey::app()->repoFactory->create('EmailAddress')->findOneBy(['id'=>$fromEmailAddressId]);

            $checkEmailAddress=$emailAddress;

            if(empty($checkEmailAddress)){
                $res = "<p style='color:red'>il sender che stai cercando di selezionare non esiste</p>";
            }else if (!$emailAddress->id->isEmpty()){
                $from = $emailAddress->address;
            }

            /** @var  $CNewsletterEmailList $newsletterEmailList */
            //ottengo  la query sql da applicare sul bacino di utenza selezionato

            $newsletterEmailList = \Monkey::app()->repoFactory->create('NewsletterEmailList')->findOneBy(['id'=>$newsletterEmailListId]);

            $checkNewsletterEmailList=$newsletterEmailList;

            if (empty($checkNewsletterUser)){
                $res = "<p style='color:red'>il filtro per il gruppo selezionato  che stai cercando non esiste</p>";
            }else if(!$newsletterEmailList->id->isEmpty()){
                $filterSql = $newsletterEmailList->sql;
                $newsletterGroupId=$newsletterEmailList->newsletterGroupid;

            }

            // ottengo la query sql del gruppo

            /** @var CNewsletterGroup $newsletterGroup */

            $newsletterGroup =\Monkey::app()->repoFactory->create('NewsletterGroup')->findOneBy(['id'=>$newsletterGroupId]);

            $checkNewsletterGroup=$newsletterGroup;

            if (empty($checkNewsletterGroup)){
                $res = "<p style='color:red'>il  gruppo selezionato  che stai cercando non esiste</p>";
            }else if(!$newsletterGroup->id->isEmpty()){
                $sqlDefault = $newsletterGroup->sql;

            }
            $sql = $sqlDefault." ". $filterSql ."group by nu.userId";


        }

        return $res;


        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings(true);


        $emailRepo = \Monkey::app()->repoFactory->create('Email');

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var \bamboo\domain\entities\CEmail $email */
            $email = $emailRepo->findOneBy($row);
            //$email = $emailRepo->findOneByStringId($email->printId());
            $row["DT_RowId"] = $email->printId();

            $error = $email->isError;
            if ($error){
                $row["DT_RowClass"] = "red";
            } else {
                $isAccepted = true;
                $isDelivered = true;
                $isPartialDelivered = false;
                $isPartialDropped = false;
                $isDropped = true;
                foreach ($email->emailRecipient as $emailRecipient){
                    /** @var CEmailRecipient $emailRecipient */
                    if (!$emailRecipient->isAccepted()) {
                        $isAccepted = false;
                        break;
                    }
                    if ($emailRecipient->isDelivered()) {
                        $isPartialDelivered = true;
                    } else {
                        $isDelivered = false;
                    }

                    if($emailRecipient->isError()) {
                        $isPartialDropped = true;
                    } else {
                        $isDropped = false;
                    }
                }

                if($isDropped) {
                    $row["DT_RowClass"] = "red";
                    //rosso
                } elseif($isPartialDropped) {
                } elseif ($isDelivered) {
                    $row["DT_RowClass"] = "green";
                    //verde
                } elseif ($isPartialDelivered) {
                    $row["DT_RowClass"] = "yellow";
                    //giallo
                } elseif($isAccepted) {
                    $row["DT_RowClass"] = "grey";
                    //grigio
                } else {
                    $row["DT_RowClass"] = "violet";
                    //rosso
                }
            }

            $row['from'] = $email->fromEmailAddress->getPrettyEmailAddress();
            $row['to'] = str_replace(',','<br />',$row['to']);
            $row['cc'] = str_replace(',','<br />',$row['cc']);
            $row['bcc'] = str_replace(',','<br />',$row['bcc']);
            $row['htmlBody'] = substr(trim(strip_tags($row['htmlBody'])), 0,50)."...";
            $row['responseTime'] = $this->secToTime($row['responseTime']);


            $datatable->setResponseDataSetRow($key,$row);

        }

        return $datatable->responseOut();
    }
}