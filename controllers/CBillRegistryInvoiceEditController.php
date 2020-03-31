<?php
namespace bamboo\blueseal\controllers;

use bamboo\ecommerce\views\VBase;
use bamboo\core\asset\CAssetCollection;
use bamboo\core\router\CInternalRequest;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class CBillRegistryInvoiceAddController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 19/02/2020
 * @since 1.0
 */


class CBillRegistryInvoiceEditController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "bill_registryinvoice_edit";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/bill_registryinvoice_edit.php');
        $id =\Monkey::app()->router->request()->getRequestData('id');
        $bri=\Monkey::app()->repoFactory->create('BillRegistryInvoice')->findOneBy(['id'=>$id]);
        $brc=\Monkey::app()->repoFactory->create('BillRegistryClient')->findOneBy(['id'=>$bri->billRegistryClientId]);
        $brir=\Monkey::app()->repoFactory->create('BillRegistryInvoiceRow')->findBy(['billRegistryInvoiceId'=>$bri->invoiceNumber]);
        $brtp=\Monkey::app()->repoFactory->create('BillRegistryTypePayment')->findAll();
        $brca=\Monkey::app()->repoFactory->create('BillRegistryClientAccount')->findOneBy(['billRegistryClientId'=>$bri->billRegistryClientId]);
        $brcbi=\Monkey::app()->repoFactory->create('BillRegistryClientBillingInfo')->findOneBy(['id'=>$bri->billRegistryClientBillingInfoId]);
        $brtt=\Monkey::app()->repoFactory->create('BillRegistryTimeTable')->findAll();

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'bri' => $bri,
            'brc' => $brc,
            'brir' => $brir,
            'brtp' => $brtp,
            'brca' =>$brca,
            'brcbi'=>$brcbi,
            'brttt'=> $brtt,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}