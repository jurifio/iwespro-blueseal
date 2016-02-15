<?php
namespace bamboo\controllers\ajax;

use bamboo\blueseal\business\CWidgetStructureParser;
use bamboo\core\intl\CLang;
use bamboo\ecommerce\views\widget\VBase;

/**
 * Class CContentHomePageListAjaxController
 * @package bamboo\htdocs\pickyshop\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 18/11/2015
 * @since 1.0
 */
class CContentHomePageListAjaxController extends AAjaxController
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

        if ($this->app->getUser()->hasRole('ownerEmployee')) {
        } else if($this->app->getUser()->hasRole('friendEmployee')){
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
        $view->setTemplatePath($bluesealPath.'/template/widgets/section.php');
        $this->app->setLang(new CLang(1,'it'));

        $repo = $this->app->repoFactory->create('Lang');
        $installedLang = $repo->findAll();

        $structure = new CWidgetStructureParser($this->app,$installedLang,'homepage');
        return $structure->getDTJson();
    }
}