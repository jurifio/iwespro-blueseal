<?php
namespace bamboo\domain\repositories;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooInvoiceException;
use bamboo\domain\entities\CAddressBook;
use bamboo\domain\entities\CContractDetails;
use bamboo\domain\entities\CDocument;
use bamboo\domain\entities\CFoison;
use bamboo\domain\entities\CInvoiceLine;
use bamboo\domain\entities\CInvoiceNumber;
use bamboo\domain\entities\CInvoiceSectional;
use bamboo\domain\entities\CInvoiceType;
use bamboo\utils\price\SPriceToolbox;
use bamboo\domain\entities\COrderLine;
use bamboo\utils\time\STimeToolbox;

/**
 * Class CContractDetailsRepo
 * @package bamboo\domain\repositories
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 16/03/2018
 * @since 1.0
 */
class CInstragramPublishRepo extends ARepo
{
    /**
     * @param $url
     * @param $post_data
     * @param $post
     * @param $user_agent
     * @param $cookies
     */
 public    function SendRequest($url, $post, $post_data, $user_agent, $cookies) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://i.instagram.com/api/v1/'.$url);
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        if($post) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        }

        if($cookies) {
            curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
        } else {
            curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
        }

        $response = curl_exec($ch);
        $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return array($http, $response);
    }

  public  function GenerateGuid() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(16384, 20479),
            mt_rand(32768, 49151),
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 65535));
    }

    function GenerateUserAgent() {
        $resolutions = array('720x1280', '320x480', '480x800', '1024x768', '1280x720', '768x1024', '480x320');
        $versions = array('GT-N7000', 'SM-N9000', 'GT-I9220', 'GT-I9100');
        $dpis = array('120', '160', '320', '240');

        $ver = $versions[array_rand($versions)];
        $dpi = $dpis[array_rand($dpis)];
        $res = $resolutions[array_rand($resolutions)];

        return 'Instagram 4.'.mt_rand(1,2).'.'.mt_rand(0,2).' Android ('.mt_rand(10,11).'/'.mt_rand(1,3).'.'.mt_rand(3,5).'.'.mt_rand(0,5).'; '.$dpi.'; '.$res.'; samsung; '.$ver.'; '.$ver.'; smdkc210; en_US)';
    }

  public  function GenerateSignature($data) {
        return hash_hmac('sha256', $data, 'b4a23f5e39b5929e0666ac5de94c89d1618a2916');
    }

   public  function GetPostData($filename) {
        if(!$filename) {
            echo "The image doesn't exist ".$filename;
        } else {
            $post_data = array('device_timestamp' => time(),
                'photo' => '@'.$filename);
            return $post_data;
        }
    }
}