<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;


/**
 * Class CNameTranslateLangListController
 * @package redpanda\blueseal\controllers
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CNameTranslateLangListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "name_lang_list";

    public function get()
    {
        $view = new VBase(array());
        $allShops = \Monkey::app()->getUser()->hasPermission('allShops');
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/name_lang_list.php');
        $this->urls['base'] = $this->app->baseUrl(false)."/blueseal/";

        $ids = $this->app->router->request()->getRequestData();
        $params = '';

        if(!empty($ids)){
            foreach ($ids as $id){
                $params .= $id.',';
            }
            $params = substr($params, 0, -1);
        }


        $langId = $this->app->router->getMatchedRoute()->getComputedFilter('langId');


        $urlAll = $this->urls['base']."traduzioni/nomi/lingua_list/" .$langId;
        $urlTrans = $this->urls['base']."traduzioni/nomi/lingua/" .$langId;

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'langId' => $langId,
            'allShops'=>$allShops,
            'page'=>$this->page,
            'urlAll'=>$urlAll,
            'urlTrans'=>$urlTrans,
            'ids'=>$params,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}