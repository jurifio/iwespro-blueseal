<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\domain\entities\CEditorialPlan;
use bamboo\domain\entities\CEditorialPlanDetail;
use bamboo\domain\entities\CEditorialPlanSocial;
use bamboo\ecommerce\views\VBase;

/**
 * Class CEditorialPlanDetailAddController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 13/08/2020
 * @since 1.0
 */
class CEditorialPlanDetailEditController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "editorialplandetail_edit";

    public function get()
    {


        //$editorialPlanDetId = \Monkey::app()->router->request()->getRequestData('id');

        //trovi il piano editoriale
        /** @var ARepo $ePlanRepo */
        $ePlanRepo = \Monkey::app()->repoFactory->create('EditorialPlan');
        /** @var ARepo $editorialPlanDetailRepo */
        $editorialPlanDetailRepo=\Monkey::app()->repoFactory->create('EditorialPlanDetail');
        $editorialPlanDetailId =  \Monkey::app()->router->getMatchedRoute()->getComputedFilter('id');
        $editorialPlanDetail=$editorialPlanDetailRepo->findOneBy(['id'=>$editorialPlanDetailId]);


        /** @var aRepo $ePlanSocialRepo */
        $ePlanSocialRepo = \Monkey::app()->repoFactory->create('EditorialPlanSocial');
         /** @var CEditorialPlanSocial $editorialPlanSocial */
         $editorialPlanSocial=$ePlanSocialRepo->findAll();

        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/editorialplandetail_edit.php');

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page'=>$this->page,
            'editorialPlanDetail'=>$editorialPlanDetail,
            'sidebar' => $this->sidebar->build()

        ]);
    }
}