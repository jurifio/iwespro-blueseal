<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\domain\entities\CEditorialPlan;
use bamboo\ecommerce\views\VBase;

/**
 * Class CAddressBookListController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CEditorialPlanDetailListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "editorialplandetail_list";

    public function get()
    {

        $editorialPlanId = \Monkey::app()->router->getMatchedRoute()->getComputedFilter('id');

        //trovi il piano editoriale
        /** @var ARepo $ePlanRepo */
        $ePlanRepo = \Monkey::app()->repoFactory->create('EditorialPlan');

        /** @var CEditorialPlan $editorialPlan */
        $editorialPlan = $ePlanRepo->findOneBy(['id'=>$editorialPlanId]);

        /** @var CObjectCollection $editorialDetails */
       // $editorialDetails = $editorialPlan->editorialPlanDetail;

        //trovi tutti i dettagli


        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/editorialplandetail_list.php');

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page'=>$this->page,
            'sidebar' => $this->sidebar->build(),
           // 'editorialDetails' => $editorialDetails,
        ]);
    }
}