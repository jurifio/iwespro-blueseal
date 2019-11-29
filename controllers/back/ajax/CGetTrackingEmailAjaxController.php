<?php

namespace bamboo\controllers\back\ajax;
use bamboo\business\carrier\ACarrierHandler;
use bamboo\business\carrier\IImplementedPickUpHandler;
use bamboo\core\base\CObjectCollection;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooShipmentException;
use bamboo\domain\entities\CCarrier;
use bamboo\business\carrier;
use bamboo\core\exceptions\BambooOrderLineException;
use bamboo\domain\entities\COrderLine;
use bamboo\domain\repositories\COrderLineRepo;
use bamboo\domain\repositories\CShipmentRepo;
use bamboo\utils\time\STimeToolbox;
use bamboo\domain\entities\COrder;
use bamboo\domain\entities\CShipment;
use DateTime;
use PDO;
use PDOException;
use Mailgun\Mailgun;

/**
 * Class CGetTrackingDeliveryAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 21/11/2019
 * @since 1.0
 */
class CGetTrackingEmailAjaxController extends AAjaxController
{
    public function get()
    {
        if(ENV == 'dev') {
            require '/media/sf_sites/vendor/mailgun/vendor/autoload.php';
        }else{
            require '/home/shared/vendor/mailgun/vendor/autoload.php';
        }


        $request = \Monkey::app()->router->request();

        $orderId = $request->getRequestData('orderId');
        $orderId=trim($orderId);
        $trackLine = [];
        $orderRepo = \Monkey::app()->repoFactory->create('Order')->findOneBy(['id' => $orderId]);
        $emailRepo =\Monkey::app()->repoFactory->create('User')->findOneBy(['id'=>$orderRepo->userId]);
        $email =$emailRepo->email;
        $messageId='message-id';
        $dateformat=strtotime($orderRepo->orderDate);
        $beginDate=date("D,d M Y H:i:s -0000",$dateformat);
        $endDate=date("D,d M Y H:i:s -0000");
        $mgClient = new Mailgun('key-1d5fe7e72fab58615be0d245d90e9e56');
        $domain = 'iwes.pro';
        $queryString = array(
            'begin'        => $beginDate,
            'end'          =>$endDate,
            'ascending'    => 'yes',
            'pretty'       => 'yes',
            'recipient'    => $email,
            'event'        => 'delivered'
        );

# Make the call to the client.
        $result = $mgClient->get("$domain/events", $queryString);


        foreach ($result->http_response_body->items as $list ) {
            if(!empty($list->timestamp)) {
                $oraInvio = $list->timestamp;
            }else{
                $oraInvio='';
            }
            if (!empty($list->envelope->sender)) {
                $sender= $list->envelope->sender;
            }else{
                $sender='';
            }
            if (!empty($list->envelope->targets)) {
                $targets=$list->envelope->targets;
            }else{
                $targets='';
            }
            if (!empty($list->message->headers->to)) {
                $to=$list->message->headers->to;
            }else{
                $to='';
            }
            if (!empty($list->message->headers->from)) {
                $from=$list->message->headers->from;
            }else{
                $from='';
            }
            if (!empty($list->message->headers->subject)) {
                $subject=$list->message->headers->subject;
            }else{
                $subject='';
            }
            if (!empty($list->message->headers->$messageId)) {

                $link="<a target='_blank' href='/blueseal/xhr/emailViewListAjaxController?messageId=" . $list->message->headers->$messageId . "&orderId=".$orderId."'>link</a><br />";
            }else{
                $link='';
            }
            if ($list->event=='Delivered') {
                array_push($trackLine,[
                    'oraInvio' => date('d-m-Y H:s:i',$oraInvio),
                    'sender' => $sender,
                    'targets' => $targets,
                    'from' => $from,
                    'to' => $to,
                    'subject' => $subject,
                    'link' => $link
                ]);
            }

        }
        $mgClient = new Mailgun('key-1d5fe7e72fab58615be0d245d90e9e56');
        $domain = 'pickyshop.com';
        $queryString = array(
            'begin'        => $beginDate,
            'end'          =>$endDate,
            'ascending'    => 'yes',
            'pretty'       => 'yes',
            'recipient'    => $email,
            'event'        => 'delivered'
        );

# Make the call to the client.
        $result = $mgClient->get("$domain/events", $queryString);


        foreach ($result->http_response_body->items as $list ) {
            if(!empty($list->timestamp)) {
                $oraInvio = $list->timestamp;
            }else{
                $oraInvio='';
            }
            if (!empty($list->envelope->sender)) {
                $sender= $list->envelope->sender;
            }else{
                $sender='';
            }
            if (!empty($list->envelope->targets)) {
                $targets=$list->envelope->targets;
            }else{
                $targets='';
            }
            if (!empty($list->message->headers->to)) {
                $to=$list->message->headers->to;
            }else{
                $to='';
            }
            if (!empty($list->message->headers->from)) {
                $from=$list->message->headers->from;
            }else{
                $from='';
            }
            if (!empty($list->message->headers->subject)) {
                $subject=$list->message->headers->subject;
            }else{
                $subject='';
            }
            if (!empty($list->message->headers->$messageId)) {

                $link="<a target='_blank' href='/blueseal/xhr/emailViewListAjaxController?messageId=" . $list->message->headers->$messageId . "&orderId=".$orderId."'>link</a><br />";
            }else{
                $link='';
            }
            array_push($trackLine,[
                'oraInvio'=>date('d-m-Y H:s:i',$oraInvio),
                'sender'=>$sender,
                'targets'=>$targets,
                'from'=>$from,
                'to'=>$to,
                'subject'=>$subject,
                'link'=>$link
            ]);

        }
        $mgClient = new Mailgun('key-1d5fe7e72fab58615be0d245d90e9e56');
        $domain = 'barbagalloshop.com';
        $queryString = array(
            'begin'        => $beginDate,
            'end'          =>$endDate,
            'ascending'    => 'yes',
            'pretty'       => 'yes',
            'recipient'    => $email,
            'event'        => 'delivered'
        );

# Make the call to the client.
        $result = $mgClient->get("$domain/events", $queryString);


        foreach ($result->http_response_body->items as $list ) {
            if(!empty($list->timestamp)) {
                $oraInvio = $list->timestamp;
            }else{
                $oraInvio='';
            }
            if (!empty($list->envelope->sender)) {
                $sender= $list->envelope->sender;
            }else{
                $sender='';
            }
            if (!empty($list->envelope->targets)) {
                $targets=$list->envelope->targets;
            }else{
                $targets='';
            }
            if (!empty($list->message->headers->to)) {
                $to=$list->message->headers->to;
            }else{
                $to='';
            }
            if (!empty($list->message->headers->from)) {
                $from=$list->message->headers->from;
            }else{
                $from='';
            }
            if (!empty($list->message->headers->subject)) {
                $subject=$list->message->headers->subject;
            }else{
                $subject='';
            }
            if (!empty($list->message->headers->$messageId)) {

                $link="<a target='_blank' href='/blueseal/xhr/emailViewListAjaxController?messageId=" . $list->message->headers->$messageId . "&orderId=".$orderId."'>link</a><br />";
            }else{
                $link='';
            }
            array_push($trackLine,[
                'oraInvio'=>date('d-m-Y H:s:i',$oraInvio),
                'sender'=>$sender,
                'targets'=>$targets,
                'from'=>$from,
                'to'=>$to,
                'subject'=>$subject,
                'link'=>$link
            ]);

        }
        $mgClient = new Mailgun('key-1d5fe7e72fab58615be0d245d90e9e56');
        $domain = 'cartechinishop.com';
        $queryString = array(
            'begin'        => $beginDate,
            'end'          =>$endDate,
            'ascending'    => 'yes',
            'pretty'       => 'yes',
            'recipient'    => $email,
            'event'        => 'delivered'
        );

# Make the call to the client.
        $result = $mgClient->get("$domain/events", $queryString);


        foreach ($result->http_response_body->items as $list ) {
            if(!empty($list->timestamp)) {
                $oraInvio = $list->timestamp;
            }else{
                $oraInvio='';
            }
            if (!empty($list->envelope->sender)) {
                $sender= $list->envelope->sender;
            }else{
                $sender='';
            }
            if (!empty($list->envelope->targets)) {
                $targets=$list->envelope->targets;
            }else{
                $targets='';
            }
            if (!empty($list->message->headers->to)) {
                $to=$list->message->headers->to;
            }else{
                $to='';
            }
            if (!empty($list->message->headers->from)) {
                $from=$list->message->headers->from;
            }else{
                $from='';
            }
            if (!empty($list->message->headers->subject)) {
                $subject=$list->message->headers->subject;
            }else{
                $subject='';
            }
            if (!empty($list->message->headers->$messageId)) {

                $link="<a target='_blank' href='/blueseal/xhr/emailViewListAjaxController?messageId=" . $list->message->headers->$messageId . "&orderId=".$orderId."'>link</a><br />";
            }else{
                $link='';
            }
            array_push($trackLine,[
                'oraInvio'=>date('d-m-Y H:s:i',$oraInvio),
                'sender'=>$sender,
                'targets'=>$targets,
                'from'=>$from,
                'to'=>$to,
                'subject'=>$subject,
                'link'=>$link
            ]);

        }


        return json_encode($trackLine);
    }
}