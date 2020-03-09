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


class CBillRegistryProductEditController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "bill_registryproduct_edit";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/bill_registryproduct_edit.php');
        $id = \Monkey::app()->router->request()->getRequestData('id');
        $brp=\Monkey::app()->repoFactory->create('BillRegistryProduct')->findOneBy(['id'=>$id]);
        $brgp=\Monkey::app()->repoFactory->create('BillRegistryGroupProduct')->findAll();
        $brgpSelect=\Monkey::app()->repoFactory->create('BillRegistryGroupProduct')->findOneBy(['id'=>$brp->billRegistryGroupProductId]);
        $brcp=\Monkey::app()->repoFactory->create('BillRegistryCategoryProduct')->findAll();
        $brcpSelect=\Monkey::app()->repoFactory->create('BillRegistryCategoryProduct')->findOneBy(['id'=>$brgpSelect->billRegistryCategoryProductId]);
        $brtt=\Monkey::app()->repoFactory->create('BillRegistryTypeTaxes')->findAll();
        $brpd=\Monkey::app()->repoFactory->create('BillRegistryProductDetail')->findBy(['billRegistryProductId'=>$brp->id]);

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'brgp'=>$brgp,
            'brp'=>$brp,
            'brcp'=>$brcp,
            'brtt'=>$brtt,
            'brgpSelect'=>$brgpSelect,
            'brcpSelect'=>$brcpSelect,
            'brpd'=>$brpd,
            'sidebar' => $this->sidebar->build()

        ]);
    }
}