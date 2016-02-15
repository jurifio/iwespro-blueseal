<?php
namespace bamboo\blueseal\controllers

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;
use bamboo\core\asset\CAssetCollection;

/**
 * Class CSizeListController
 * @package bamboo\blueseal\controllers
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CSizeListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "product_sizegroup_list";

    public function get()
    {
        $ac = new CAssetCollection();
        $view = new VBase(array());
        $view->setTemplatePath($this->app->cfg()->fetch('paths','blueseal').'/template/sizegroup_list.php');

        $blueseal = $this->app->baseUrl(false).'/blueseal/';
        $pageURL = $blueseal."/sizes";
        $deleteError = false;
        $res = 0;
        if(isset($_GET)) {
            if (isset($_GET['del']) && $_GET['del'] == 'Y') {
                try {
                    $mysql = $this->app->dbAdapter;
                    $res = $mysql->delete("ProductSizeGroupHasProductSize", array("productSizeGroupId" => $_GET['productSizeGroupId']));
                    $res=  $mysql->delete("ProductSizeGroup", array("id" => $_GET['productSizeGroupId']));
                    if (!headers_sent()) {
                        header("Location: ".$pageURL."?delete=ok");
                    }
                } catch (\Exception $e) {
                    if (!headers_sent()) {
                        header("Location: ".$pageURL."?delete=ko");
                    }
                }
            }
            if(isset($_GET['delete']) && $_GET['delete'] == 'ko') $deleteError = true;
        }
        $em = $this->app->entityManagerFactory->create('ProductSizeGroup');
        $groups = $em->findBySql("SELECT distinct id from ProductSizeGroup GROUP BY macroName ",[]);
        $modifica = $blueseal."prodotti/gruppo-taglie/aggiungi";

        echo $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'groups' => $groups,
            'modifica' => $modifica,
            'deleteError' => $deleteError,
            'pageURL' => $pageURL,
            'res' => $res,
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }

    public function post()
    {

    }
}