<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CWidgetStructureParser;
use bamboo\core\intl\CLang;
use bamboo\ecommerce\views\widget\VBase;


/**
 * Class CContentCatalogListAjaxController
 * @package bamboo\blueseal\controllers\ajax
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
class CContentCatalogListAjaxController extends AAjaxController
{
    /**
     * @var array
     */
    protected $urls = [];
    /**
     * @var array
     */
    protected $authorizedShops = [];
    /**
     * @var
     */
    protected $em;

    /**
     * @param $action
     * @return mixed
     */
    public function createAction($action)
    {
        $this->app->setLang(new CLang(1,'it'));
        $this->urls['base'] = $this->app->baseUrl(false)."/blueseal/";
        $this->urls['page'] = $this->urls['base']."prodotti";
        $this->urls['dummy'] = $this->app->cfg()->fetch('paths','dummyUrl');

        if ($this->app->getUser()->hasPermission('allShops')) {
        } else{
            $res = $this->app->dbAdapter->select('UserHasShop',['userId'=>$this->app->getUser()->getId()])->fetchAll();
            foreach($res as $val) {
                $this->authorizedShops[] = $val['shopId'];
            }
        }

        return $this->{$action}();
    }

    /**
     * @return string
     */
    public function get()
    {
        $bluesealPath = $this->app->cfg()->fetch('paths','blueseal');

        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$bluesealPath.'/template/widgets/section.php');
        $this->app->setLang(new CLang(1,'it'));

        $repo = \Monkey::app()->repoFactory->create('Lang');
        $installedLang = $repo->findAll();

        $structure = new CWidgetStructureParser($this->app,$installedLang,'catalog');
        return $structure->getDTJson();
    }
}