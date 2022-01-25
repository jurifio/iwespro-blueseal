<?php

namespace bamboo\controllers\back\ajax;


use bamboo\domain\entities\CShop;
use bamboo\core\exceptions\BambooException;
use bamboo\core\utils\cpanel\createCPanelAccount;


/**
 * Class CCreateCPanelAccountAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 25/01/2022
 * @since 1.0
 */
class CCreateCPanelAccountAjaxController extends AAjaxController
{
    public function get()
    {

    }

    public
    function put()
    {

    }
    public
    function post()
    {
        $data = $this->app->router->request()->getRequestData();
        $newDomain=$data['ftpHost'];
        $newUser=$data['ftpUser'];
        $newPassword=$data['ftpPassword'];
        $emailUser=$data['emailUser'];
        try {
            $createAccount =   new createCPanelAccount();
            $result=$createAccount->createAccount($newDomain,$newUser,$newPassword,'default',$emailUser,'root','F1fiI3EYv9JXl8Z','front.iwes.it');

            return $result;
        }
        catch (Exception $e) {
            echo $e->getMessage() . "\n";
        }
    }


}