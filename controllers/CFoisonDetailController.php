<?php
namespace bamboo\blueseal\controllers;

use bamboo\domain\entities\CFoison;
use bamboo\ecommerce\views\VBase;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class CFoisonDetailController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 08/05/2018
 * @since 1.0
 */
class CFoisonDetailController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "foison_detail";

    /**
     * @return string
     * @throws \bamboo\core\exceptions\RedPandaInvalidArgumentException
     */
    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/foison_detail.php');

        $foisonId = $this->app->router->getMatchedRoute()->getComputedFilter('id');
/** @var CFoison $foison */
        $foison = \Monkey::app()->repoFactory->create('Foison')->findOneBy(['id'=>$foisonId]);

        $userAddress = \Monkey::app()->repoFactory->create('UserAddress')->findOneBy(['userId' => $foison->userId]);


        $workCategories ='';

        /** @var \bamboo\domain\entities\CFoisonHasInterest $interests */
        $interests = $foison->foisonHasInterest;

        $allPB = $foison->getClosedTimeRanchProductBatch();

        /** @var \bamboo\domain\entities\CFoisonHasInterest $interest */
        foreach ($interests as $interest) {
            $allPbForCat = $allPB->findByKey("workCategoryId",$interest->workCategoryId);

            $r = !empty($allPbForCat) ?  "<strong>" . $foison->totalRank(false, $allPbForCat) . "</strong><br><br>" : '<strong>'."0".'</strong>'.'<br><br>';

            $workCategories .= $interest->workCategory->name . ":<br> 
                                Stato: " . "<strong>" . $interest->foisonStatus->name . "</strong><br> 
                                Rank: " . $r;
        }

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'foison' => $foison,
            'langs' => \Monkey::app()->repoFactory->create('Lang')->findAll(),
            'country' => \Monkey::app()->repoFactory->create('Country')->findAll(),
            'userAddress' => $userAddress,
            'sidebar' => $this->sidebar->build(),
            'workCategories'=>$workCategories
        ]);
    }
}