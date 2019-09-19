<?php

namespace bamboo\controllers\back\ajax;

use Aws\DynamoDb\Model\Attribute;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\email\CEmail;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooORMInvalidEntityException;
use bamboo\core\exceptions\BambooORMReadOnlyException;
use bamboo\domain\entities\CEmailAddress;
use bamboo\domain\entities\CNewsletter;
use bamboo\domain\entities\CNewsletterCampaign;
use bamboo\domain\entities\CNewsletterEvent;
use bamboo\domain\entities\CNewsletterInsertion;
use bamboo\domain\entities\CNewsletterTemplate;
use bamboo\domain\entities\CNewsletterUser;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\domain\repositories\CNewsletterRepo;


/**
 * Class CEmailUserServiceManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 19/09/2019
 * @since 1.0
 */
class CEmailUserServiceManage extends AAjaxController
{
    /**
     * @return int
     * @throws BambooException
     * @throws \Exception
     */
    public function post()
    {
        //prendo i dati passati in input
        $data = \Monkey::app()->router->request()->getRequestData();
        $typeOperation = $data['typeOperation'];

            $fromEmailAddressId = $data['fromEmailAddressId'];
            $sendAddressDate = $data['sendAddressDate'];
            $newsletterTemplateId = "1";
            $emailTo=$data['emailTo'];
            $subject = $data['subject'];
            $preCompiledTemplate = $data['preCompiledTemplate'];

        if ($typeOperation === "1") {

            $to = explode(';',$data['emailTo']);
            $message = $data['preCompiledTemplate'];
            $subject = $data['subject'];
            $fromEmailAddressId = $data['fromEmailAddressId'];
            $userRepo=\Monkey::app()->repoFactory->create('User')->findOneBy(['id'=>$fromEmailAddressId]);
            $from=$userRepo->email;
            //  if (ENV == 'dev') return false;
            /** @var \bamboo\domain\repositories\CEmailRepo $emailRepo */
            $emailRepo = \Monkey::app()->repoFactory->create('Email');

            $emailRepo->newMail($from, $to, [], [], $subject, $message);
            $res = "Email Inviata con successo";


        } else {
            $to = $data['toEmailAddressTest'];
            $message = $data['preCompiledTemplate'];
            $subject = $data['subject'];
            $fromEmailAddressId = $data['fromEmailAddressId'];
            $userRepo=\Monkey::app()->repoFactory->create('User')->findOneBy(['id'=>$fromEmailAddressId]);
            $from=$userRepo->email;

            //  if (ENV == 'dev') return false;
            /** @var \bamboo\domain\repositories\CEmailRepo $emailRepo */
            $emailRepo = \Monkey::app()->repoFactory->create('Email');
            if (!is_array($to)) {
                $to = [$to];

            }
            $emailRepo->newMail($from, $to, [], [], $subject, $message);
            $res = "Test Inviato con successo";
        }

        return $res;


    }

    public function secToTime($init)
    {
        $hours = floor($init / 3600);
        $minutes = floor(($init / 60) % 60);
        $seconds = $init % 60;

        return "$hours:$minutes:$seconds";
    }

    public function put()
    {
        $data = \Monkey::app()->router->request()->getRequestData();
        $idNewsletterUser = $data['idNewsletterUser'];

        /** @var CNewsletterRepo $newsletterRepo */
        $newsletterRepo = \Monkey::app()->repoFactory->create('Newsletter');
        /** @var CNewsletter $newsletterUser */
        $newsletterUser = $newsletterRepo->findOneBy(['id' => $idNewsletterUser]);
        $isTest = true;
        return $newsletterRepo->sendNewsletterEmails($newsletterUser, $isTest);
    }


}