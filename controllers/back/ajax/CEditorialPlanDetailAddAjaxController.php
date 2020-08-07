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
use FacebookAds\Logger\CurlLogger;
use FacebookAds\Object\AdSet;
use FacebookAds\Object\Fields\AdSetFields;
use FacebookAds\Object\Values\AdSetBillingEventValues;
use FacebookAds\Object\Values\AdSetOptimizationGoalValues;
use bamboo\domain\repositories\CInstragramPublishRepo;

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
                    // Set the username and password of the account that you wish to post a photo to
                    $username = 'fioranijuri';
                    $password = 'Zora231074!';

// Set the path to the file that you wish to post.
// This must be jpeg format and it must be a perfect square
                    $filename = $groupimage;

// Set the caption for the photo
                    $caption = $title;

// Define the user agent
                    $agent = GenerateUserAgent();

// Define the GuID
                    $guid = GenerateGuid();

// Set the devide ID
                    $device_id = "android-".$guid;

                    /* LOG IN */
// You must be logged in to the account that you wish to post a photo too
// Set all of the parameters in the string, and then sign it with their API key using SHA-256
                    $data ='{"device_id":"'.$device_id.'","guid":"'.$guid.'","username":"'.$username.'","password":"'.$password.'","Content-Type":"application/x-www-form-urlencoded; charset=UTF-8"}';
                    $sig = GenerateSignature($data);
                    $data = 'signed_body='.$sig.'.'.urlencode($data).'&ig_sig_key_version=4';
                    $login = SendRequest('accounts/login/', true, $data, $agent, false);

                    if(strpos($login[1], "Sorry, an error occurred while processing this request.")) {
                        echo "Request failed, there's a chance that this proxy/ip is blocked";
                    } else {
                        if(empty($login[1])) {
                            echo "Empty response received from the server while trying to login";
                        } else {
                            // Decode the array that is returned
                            $obj = @json_decode($login[1], true);

                            if(empty($obj)) {
                                echo "Could not decode the response: ".$body;
                            } else {
                                // Post the picture
                                $data = GetPostData($filename);
                                $post = SendRequest('media/upload/', true, $data, $agent, true);

                                if(empty($post[1])) {
                                    echo "Empty response received from the server while trying to post the image";
                                } else {
                                    // Decode the response
                                    $obj = @json_decode($post[1], true);

                                    if(empty($obj)) {
                                        echo "Could not decode the response";
                                    } else {
                                        $status = $obj['status'];

                                        if($status == 'ok') {
                                            // Remove and line breaks from the caption
                                            $caption = preg_replace("/\r|\n/", "", $caption);

                                            $media_id = $obj['media_id'];
                                            $device_id = "android-".$guid;
                                            $data = '{"device_id":"'.$device_id.'","guid":"'.$guid.'","media_id":"'.$media_id.'","caption":"'.trim($caption).'","device_timestamp":"'.time().'","source_type":"5","filter_type":"0","extra":"{}","Content-Type":"application/x-www-form-urlencoded; charset=UTF-8"}';
                                            $sig = GenerateSignature($data);
                                            $new_data = 'signed_body='.$sig.'.'.urlencode($data).'&ig_sig_key_version=4';

                                            // Now, configure the photo
                                            $conf = SendRequest('media/configure/', true, $new_data, $agent, true);

                                            if(empty($conf[1])) {
                                                echo "Empty response received from the server while trying to configure the image";
                                            } else {
                                                if(strpos($conf[1], "login_required")) {
                                                    echo "You are not logged in. There's a chance that the account is banned";
                                                } else {
                                                    $obj = @json_decode($conf[1], true);
                                                    $status = $obj['status'];

                                                    if($status != 'fail') {
                                                        echo "Success";
                                                    } else {
                                                        echo 'Fail';
                                                    }
                                                }
                                            }
                                        } else {
                                            echo "Status isn't okay";
                                        }
                                    }
                                }
                            }
                        }
                    }

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