<?php
namespace bamboo\blueseal\controllers;

use bamboo\ecommerce\views\VBase;
use bamboo\core\asset\CAssetCollection;
use bamboo\core\router\CInternalRequest;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class CBillRegistryGroupProductAddController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 10/02/2020
 * @since 1.0
 */


class CBillRegistryGroupProductEditController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "bill_registrygroupproduct_edit";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/bill_registrygroupproduct_edit.php');
        $id = \Monkey::app()->router->request()->getRequestData('id');
        $brgp=\Monkey::app()->repoFactory->create('BillRegistryGroupProduct')->findOneBy(['id'=>$id]);
        $brg=\Monkey::app()->repoFactory->create('BillRegistryGroupProduct')->findAll();
        $brtt=\Monkey::app()->repoFactory->create('BillRegistryTypeTaxes')->findAll();

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'brgp'=>$brgp,
            'brg'=>$brg,
            'brtt'=>$brtt,
            'sidebar' => $this->sidebar->build()

        ]);
    }
}