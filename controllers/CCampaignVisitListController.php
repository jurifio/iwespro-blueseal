<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;
use bamboo\core\db\pandaorm\entities\CEntityManager;

/**
 * Class CCampaignVisitController
 * @package bamboo\blueseal\controllers
 */
class CCampaignVisitListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "campaignvisit_list";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/campaignvisit_list.php');
        $campaignId = \Monkey::app()->router->request()->getRequestData('id');
        if($campaignId==null) {
            $campaignId = '';
        }

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'campaignId'=>$campaignId,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}