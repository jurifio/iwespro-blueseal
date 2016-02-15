<?php
namespace bamboo\blueseal\controllers

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;
use bamboo\core\asset\CAssetCollection;
use bamboo\core\db\pandaorm\entities\CEntityManager;
use bamboo\core\db\pandaorm\adapter\CMySQLAdapter;

/**
 * Class CColorListController
 * @package bamboo\app\controllers
 */
class CColorListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "product_colorgroup_list";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->cfg()->fetch('paths','blueseal').'/template/colorgroup_list.php');

        $blueseal = $this->app->baseUrl(false).'/blueseal';
        $pageURL = $blueseal."/prodotti/gruppo-colore";
        $deleteError = false;
        $res = null;

        /** @var $em CEntityManager **/
        $em = $this->app->entityManagerFactory->create('ProductColorGroup');

        /** @var $mysql CMySQLAdapter **/
        $gruppicolore = $em->findBySql("select * from ProductColorGroup where langId = 1",array());

        $modifica = $blueseal."/prodotti/gruppo-colore/modifica";
        echo $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'gruppicolore' => $gruppicolore,
            'modifica' => $modifica,
            'deleteError' => $deleteError,
            'pageURL' => $pageURL,
            'res' => $res,
            'addUrl' => $pageURL.'/aggiungi',
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}