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
        if (ENV == 'dev') {
            require '/media/sf_sites/vendor/mailgun/vendor/autoload.php';
        } else {
            require '/home/shared/vendor/mailgun/vendor/autoload.php';
        }


        $request = \Monkey::app()->router->request();

        $orderId = $request->getRequestData('orderId');
        $orderId = trim($orderId);
        $trackLine = [];
        $order = \Monkey::app()->repoFactory->create('Order')->findOneBy(['id' => $orderId]);
        $remoteShopSellerId = $order->remoteShopSellerId;
        $user = \Monkey::app()->repoFactory->create('User')->findOneBy(['id' => $order->userId]);
        $userinfo = \Monkey::app()->repoFactory->create('UserDetails')->findOneBy(['userId' => $user->id]);
        $email = $user->email;
        $sql = 'SELECT e.id AS id,
 e.subject AS subject,
  (SELECT ea2.`address` from EmailAddress ea2 WHERE ea2.id=e.fromEmailAddressId) AS FromEmailAddress,
  ea.address AS toEmailAdddress,
e.htmlBody AS htmlBody,
if(e.isError=1,\'Errore\',\'Ok\') AS `isError`,
e.providerEmailId AS messageId,
 e.submissionDate
  FROM Email e JOIN EmailRecipient er ON e.id=er.emailId 
join EmailAddress ea on er.emailAddressId=ea.id WHERE  ea.address=\'' . $email.'\'';
        $res = \Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
        foreach ($res as $result) {
            $link = "<a target='_blank' href='/blueseal/xhr/emailViewListAjaxController?messageId=" . $result['id'] . "&orderId=" . $orderId . "'>link</a><br />";
            array_push($trackLine,[
                'oraInvio' => ((new \DateTime($result['submissionDate']))->format('d-m-Y H:i:s')),
                'sender' => $result['FromEmailAddress'],
                'targets' => $userinfo->name . '-' . $userinfo->surname,
                'from' => $result['FromEmailAddress'],
                'to' => $result['toEmailAdddress'],
                'subject' => $result['subject'],
                'link' => $link,
                'isError' => $result['isError']
            ]);
        }
        //email su shop di vendita
        $shop = \Monkey::app()->repoFactory->create('Shop')->findOneBy(['id' => $remoteShopSellerId]);

            $db_host = $shop->dbHost;
            $db_name = $shop->dbName;
            $db_user = $shop->dbUsername;
            $db_pass = $shop->dbPassword;
            $shopId = $shop->id;
            try {

                $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
                $db_con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                $res = " connessione ok <br>";
            } catch (PDOException $e) {
                $res = $e->getMessage();
            }
            $stmtEmail = $db_con->prepare('SELECT e.id AS id,
 e.subject AS subject,
  (SELECT ea2.`address` from EmailAddress ea2 WHERE ea2.id=e.fromEmailAddressId) AS FromEmailAddress,
  ea.address AS toEmailAdddress,
e.htmlBody AS htmlBody,
if(e.isError=1,"Errore","Ok") AS `isError`,
e.providerEmailId AS messageId,
 e.submissionDate
  FROM Email e JOIN EmailRecipient er ON e.id=er.emailId 
join EmailAddress ea on er.emailAddressId=ea.id WHERE  ea.address=\'' . $email.'\'');
$stmtEmail->execute();


          while  ($rowEmail = $stmtEmail->fetch(PDO::FETCH_ASSOC)){
              $link = "<a target='_blank' href='/blueseal/xhr/emailViewListAjaxController?messageId=" . $rowEmail['id'] . "&orderId=" . $orderId . "'>link</a><br />";
              array_push($trackLine,[
                  'oraInvio' => ((new \DateTime($rowEmail['submissionDate']))->format('d-m-Y H:i:s')),
                  'sender' => $rowEmail['FromEmailAddress'],
                  'targets' => $userinfo->name . '-' . $userinfo->surname,
                  'from' => $rowEmail['FromEmailAddress'],
                  'to' => $rowEmail['toEmailAdddress'],
                  'subject' => $rowEmail['subject'],
                  'link' => $link,
                  'isError' => $rowEmail['isError']
              ]);
          }



            return json_encode($trackLine);

    }
}