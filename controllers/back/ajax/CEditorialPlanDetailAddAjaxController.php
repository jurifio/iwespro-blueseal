<?php
/**
 * Created by PhpStorm.
 * User: jurif
 * Date: 06/04/2018
 * Time: 16:55
 */

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\email\CEmail;
use bamboo\core\facebook\CFacebookCookieSession;
use bamboo\domain\entities\CEditorialPlan;
use bamboo\domain\entities\CEditorialPlanDetail;
use bamboo\domain\entities\CEditorialPlanSocial;
use \bamboo\utils\time\STimeToolbox;
use Facebook\Facebook;
use DateTime;
use FacebookAds\Api;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\AdCreative;
use FacebookAds\Logger\CurlLogger;
use FacebookAds\Object\Ad;
use FacebookAds\Object\AdSet;
use FacebookAds\Object\Fields\AdSetFields;
use FacebookAds\Object\Values\AdSetBillingEventValues;
use FacebookAds\Object\Values\AdSetOptimizationGoalValues;
use FacebookAds\Http\RequestInterface;
use FacebookAds\Object\Values\AdCreativeCallToActionTypeValues;
use FacebookAds\Object\Fields\AdCreativeFields;
use FacebookAds\Object\Fields\AdFields;

class CEditorialPlanDetailAddAjaxController extends AAjaxController
{

    /**
     * @return bool|string
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function post()
    {
        $data = \Monkey::app()->router->request()->getRequestData();
        $title = $data['title'];
        if ($title == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> titolo non compilato</i>';
        }
        $isEventVisible = $data['isEventVisible'];
        $startEventDate = $data['start'];
        $endEventDate = $data['end'];
        $argument = $data['argument'];
        $linkDestination = $data['linkDestination'];
        $facebookCampaignId = $data['facebookCampaignId'];
        $campaignId = $data['campaignId'];
        $groupAdsName = $data['groupAdsName'];
        if ($argument == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Argomento non selezionnato</i>';
        }
        $isVisibleEditorialPlanArgument = $data['isVisibleEditorialPlanArgument'];
        $isVisiblePhotoUrl = $data['isVisiblePhotoUrl'];
        $bodyEvent = $data['bodyEvent'];
        $isVisibleBodyEvent = $data['isVisibleBodyEvent'];
        $note = $data['note'];
        $isVisibleNote = $data['isVisibleNote'];
        $description = $data['description'];
        $isVisibleDescription = $data['isVisibleDescription'];

        $photoUrl = (array_key_exists('photoUrl',$data)) ? $data['photoUrl'] : '';
        $unlinkphoto = [];
        $status = $data['status'];
        if ($status == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Non hai selezionato lo stato</i>';
        }
        $socialId = $data['socialId'];
        $editorialPlanId = $data['editorialPlanId'];
        $editorialPlan=\Monkey::app()->repoFactory->create('EditorialPlan')->findOneBy(['id'=>$data['editorialPlanId']]);
        $facebookMarketAccountId=$editorialPlan->facebookMarketAccountId;
        $editorialPlanShopAsSocial = \Monkey::app()->repoFactory->create('EditorialPlanShopAsSocial')->findOneBy(['shopId' => $editorialPlan->shopId]);
        $pageAccessToken = $editorialPlanShopAsSocial->access_token;
        $adAccountId='act_'.$facebookMarketAccountId;
        $_SESSION['facebook_access_token']=$pageAccessToken;
        Api::init(
            $fbConfig['app_id'], // App ID
            $fbConfig['app_secret'],
            $_SESSION['facebook_access_token']);
            // Your user access token
        $startEventForFacebook=STimeToolbox::FormatDateFromDBValue($startEventDate,DateTime::ISO8601);
        $endEventForFacebook=STimeToolbox::FormatDateFromDBValue($endEventDate,DateTime::ISO8601);
        $startEventDate = STimeToolbox::FormatDateFromDBValue($startEventDate,'Y-m-d H:i:s');
        $endEventDate = STimeToolbox::FormatDateFromDBValue($endEventDate,'Y-m-d H:i:s');

        $notifyEmail = $data['notifyEmail'];
        $tempFolder = $this->app->rootPath() . $this->app->cfg()->fetch('paths','tempFolder') . "-plandetail/";
        $files = glob($tempFolder . "*.jpg");
        $url = "https://iwes-editorial.s3-eu-west-1.amazonaws.com/plandetail-images/";
        /*   foreach ($files as $jpg) {

               $finalslash = strrpos($jpg, '/');
               $photo = substr($jpg, $finalslash, 1000);
               $photo = trim($photo);
               $image = $url . $photo;
               array_push($photoUrl, $image);

               array_push($unlinkphoto,$photo);


        }*/
        if ($photoUrl != '') {
            foreach ($photoUrl as &$jpg) {

                $jpg = $jpg;

            }

            /** @var array $groupimage */
            $groupimage = implode(",",$photoUrl);
        } else {
            // $groupimage='https://www.iwes.pro/assets/bs-dummy-16-9.png';
            $groupimage = '';
        }

        /** @var CRepo $editorialPlanDetailRepo */
        $editorialPlanDetailRepo = \Monkey::app()->repoFactory->create('EditorialPlanDetail');

        /** @var CEditorialPlanDetail $editorialPlanDetail */
        $editorialPlanDetail = $editorialPlanDetailRepo->findOneBy(['title' => $title]);


        if (empty($editorialPlanDetail)) {
            //se la variabile non è istanziata inserisci in db

            /** @var CEditorialPlanDetail $editorialPlanDetailInsert */
            $editorialPlanDetailInsert = \Monkey::app()->repoFactory->create('EditorialPlanDetail')->getEmptyEntity();
            //popolo la tabella

            $editorialPlanDetailInsert->title = $title;
            $editorialPlanDetailInsert->isEventVisible = $isEventVisible;
            $editorialPlanDetailInsert->startEventDate = $startEventDate;
            $editorialPlanDetailInsert->endEventDate = $endEventDate;
            $editorialPlanDetailInsert->editorialPlanArgumentId = $argument;
            $editorialPlanDetailInsert->isVisibleEditorialPlanArgument = $isVisibleEditorialPlanArgument;
            $editorialPlanDetailInsert->description = $description;
            $editorialPlanDetailInsert->isVisibleDescription = $isVisibleDescription;
            $editorialPlanDetailInsert->photoUrl = $groupimage;
            $editorialPlanDetailInsert->isVisiblePhotoUrl = $isVisiblePhotoUrl;
            $editorialPlanDetailInsert->status = $status;
            $editorialPlanDetailInsert->note = $note;
            $editorialPlanDetailInsert->linkDestination = $linkDestination;
            $editorialPlanDetailInsert->isVisibleNote = $isVisibleNote;
            $editorialPlanDetailInsert->socialId = $socialId;
            $editorialPlanDetailInsert->bodyEvent = $bodyEvent;
            if ($facebookCampaignId != 'notExist') {
                $editorialPlanDetailInsert->facebookCampaignId = $facebookCampaignId;
            }
            if ($groupAdsName != 'notExist') {
                $editorialPlanDetailInsert->groupInsertionId = $groupAdsName;
            }
            $editorialPlanDetailInsert->isVisibleBodyEvent = $isVisibleBodyEvent;
            $editorialPlanDetailInsert->editorialPlanId = $editorialPlanId;

            // eseguo la commit sulla tabella;
            switch ($argument) {
                case '4':


                    break;
                case  '5':
                    $this->app->vendorLibraries->load("facebook");
                    $c = new CFacebookCookieSession($this->app);
                    $fbConfig = $this->app->cfg()->fetch('miscellaneous','facebook');
                    $fb = new Facebook([
                        'app_id' => $fbConfig['app_id'], // Replace {app-id} with your app id
                        'app_secret' => $fbConfig['app_secret'],
                        'default_graph_version' => $fbConfig['default_graph_version'],
                        'persistent_data_handler' => &$c
                    ]);
                    $pageAccessToken =$editorialPlanShopAsSocial->access_token;

                    $linkData = [
                        'message' => $editorialPlan->name,
                        'name' => $editorialPlanDetail->title,
                        'link' => $editorialPlanDetail->linkDestination,
                        'description' => $editorialPlanDetail->description,
                        'picture' => $editorialPlanDetail->photoUrl
                    ];
                    try {
                        $response = $fb->post('/me/feed',$linkData,$pageAccessToken);
                    } catch (Facebook\Exceptions\FacebookResponseException $e) {
                        \Monkey::app()->applicationLog('CEditorialPlanDetailAddAjaxController', 'Error', 'Graph returned an error: ' . $e->getMessage(),$e->getLine(),'');

                    } catch (Facebook\Exceptions\FacebookSDKException $e) {
                        \Monkey::app()->applicationLog('CEditorialPlanDetailAddAjaxController', 'Error', 'Graph returned an error Sdk: ' . $e->getMessage(),$e->getLine(),'');

                    }
                    $graphNode = $response->getGraphNode();
                    $editorialPlanDetailInsert->insertionId=$graphNode['id'];

                    break;
                case '6':
                    break;
                case '8':
                    break;
                case '9':
                    $this->app->vendorLibraries->load("facebook");
                    $c = new CFacebookCookieSession($this->app);
                    $fbConfig = $this->app->cfg()->fetch('miscellaneous','facebook');
                    $fb = new Facebook([
                        'app_id' => $fbConfig['app_id'], // Replace {app-id} with your app id
                        'app_secret' => $fbConfig['app_secret'],
                        'default_graph_version' => $fbConfig['default_graph_version'],
                        'persistent_data_handler' => &$c
                    ]);
                    $pageAccessToken =$editorialPlanShopAsSocial->access_token;

                    $linkData = [
                        'message' => $editorialPlan->name,
                        'name' => $editorialPlanDetail->title,
                        'link' => $editorialPlanDetail->linkDestination,
                        'description' => $editorialPlanDetail->description,
                        'picture' => $editorialPlanDetail->photoUrl
                    ];
                    try {
                        $response = $fb->post('/me/feed',$linkData,$pageAccessToken);
                    } catch (Facebook\Exceptions\FacebookResponseException $e) {
                        \Monkey::app()->applicationLog('CEditorialPlanDetailAddAjaxController', 'Error', 'Graph returned an error: ' . $e->getMessage(),$e->getLine(),'');

                    } catch (Facebook\Exceptions\FacebookSDKException $e) {
                        \Monkey::app()->applicationLog('CEditorialPlanDetailAddAjaxController', 'Error', 'Graph returned an error Sdk: ' . $e->getMessage(),$e->getLine(),'');

                    }
                    $graphNode = $response->getGraphNode();
                    $editorialPlanDetailInsert->insertionId=$graphNode['id'];
                    break;

            }

            $editorialPlanDetailInsert->smartInsert();


            /*  foreach ($photoUrl as $file) {
                  unlink($tempFolder . $file);
              }*/
            $res = "Dettaglio Piano Editoriale inserito con successo!";
            /** @var ARepo $shopRepo */
            $ePlanRepo = \Monkey::app()->repoFactory->create('EditorialPlan');

            /** @var CEditorialPlan $editorialPlan */
            $editorialPlan = $ePlanRepo->findOneBy(['id' => $editorialPlanId]);

            $shopId = $editorialPlan->shop->id;
            $shopEmail = $editorialPlan->shop->referrerEmails;
            /** var ARepo $editorialPlanArgumentRepo */
            $editorialPlanArgumentRepo = \Monkey::app()->repoFactory->create('EditorialPlanArgument');

            /** @var CEditorialPlanArgument $editorialPlanArgument */
            $editorialPlanArgument = $editorialPlanArgumentRepo->findOneBy(['id' => $argument]);
            $argumentName = $editorialPlanArgument->titleArgument;
            /** @var Ceditorial $to */
            $to = $shopEmail;
            $editorialPlanName = $editorialPlan->name;
            $subject = "Creazione Nuovo Dettaglio Piano Editoriale";
            $message = "Creazione Nuovo dettaglio Piano Editoriale<p>";
            $message .= "Title:" . $title . "<p>";
            $message .= "Data di Inizio:" . $startEventDate . "<p>";
            $message .= "Data di Fine:" . $endEventDate . "<p>";
            $message .= "Argomento:" . $argumentName . "<p>";
            $message .= "Descrizione:" . $description . "<p>";
            $message .= "Stato:" . $status . "<p>";
            $message .= "Note:" . $note . "<p>";
            /** @var ARepo $ePlanSocialRepo */
            $ePlanSocialRepo = \Monkey::app()->repoFactory->create('EditorialPlanSocial');
            /** @var CEditorialPlanSocial $editorialPlanSocial */
            $editorialPlanSocial = $ePlanSocialRepo->findOneBy(['id' => $socialId]);

            /** @var CObjectCollection $editorialPlanSocialName */
            $editorialPlanSocialName = $editorialPlanSocial->name . "<p>";
            $message .= "Media utilizzato:" . $editorialPlanSocialName . "<p>";
            $message .= "Piano Editoriale:" . $editorialPlanName . "<p>";


            if ($notifyEmail === "yesNotify") {

                if (ENV == 'dev') return false;
                /** @var \bamboo\domain\repositories\CEmailRepo $emailRepo */
                $emailRepo = \Monkey::app()->repoFactory->create('Email');
                if (!is_array($to)) {
                    $to = [$to];
                }
                $emailRepo->newMail('Iwes IT Department <it@iwes.it>',$to,[],[],$subject,$message,null,null,null,'mailGun',false,null);
            }

        } else {
            //Se hai trovato qualcosa allora restituitsci messaggio di errore
            $res = "Esiste già un Evento Azione per il  piano Editoriale con lo stesso nome";
        }


        return $res;
    }


}