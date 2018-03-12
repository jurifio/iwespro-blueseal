<?php

namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;
use bamboo\core\db\pandaorm\repositories\CRepo;

/**
* Class CNewsletteruserAddController
* @package bamboo\blueseal\controllers
*
* @author Iwes Team <it@iwes.it>
*
* @copyright (c) Iwes  snc - All rights reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*
* @date 19/12/2017
* @since 1.0
*/
class CNewsletterRedemptionCompareController extends ARestrictedAccessRootController
{
protected $fallBack = "blueseal";
protected $pageSlug = "newsletter_redemption_compare";

/**
* @return string
* @throws \bamboo\core\exceptions\RedPandaInvalidArgumentException
*/

    public function get()
    {

        $data = \Monkey::app()->router->request()->getRequestData();

        $nId = $data['nId'];
        $nId2 = $data['nId2'];
        $nName = $data['nName'];
        $nName2 = $data['nName2'];
        $eAC = $data['eAC'];
        $eAC = $data['eAC2'];
        $sTime = $data['sTime'];
        $sTime2 = $data['sTime2'];
        $oTSS = $data['oTSS'];
        $oTSS2 = $data['oTSS2'];
        $cTSO = $data['cTSO'];
        $cTSO2 = $data['cTSO2'];
        $aT = $data['aT'];
        $aT2 = $data['aT2'];
        $sP = $data['sP'];
        $sP2 = $data['sP2'];
        $oP = $data['oP'];
        $oP2 = $data['oP2'];
        $cP = $data['cP'];
        $cP2 = $data['cP2'];


     $view = new VBase(array());
     $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/newsletter_redemption_compare.php');

            return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page'=>$this->page,
            'stats'=> $data,
            'sidebar' => $this->sidebar->build()
            ]);

    }
}