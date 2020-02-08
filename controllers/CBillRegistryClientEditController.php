<?php
namespace bamboo\blueseal\controllers;

use bamboo\ecommerce\views\VBase;
use bamboo\core\asset\CAssetCollection;
use bamboo\core\router\CInternalRequest;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class CBillRegistryClientEditController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 18/07/2018
 * @since 1.0
 */


class CBillRegistryClientEditController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "bill_registryclient_edit";

    public function get()
    {

        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/bill_registryclient_edit.php');
        $id = \Monkey::app()->router->request()->getRequestData('id');
        $billRegistryClientRepo = \Monkey::app()->repoFactory->create('BillRegistryClient');
        $billRegistryClientAccountRepo = \Monkey::app()->repoFactory->create('BillRegistryClientAccount');
        $billRegistryClientLocationRepo= \Monkey::app()->repoFactory->create('BillRegistryClientLocation');
        $billRegistryGroupProduct=\Monkey::app()->repoFactory->create('BillRegistryGroupProduct');
        $billRegistryClientContactRepo=\Monkey::app()->repoFactory->create('BillRegistryContact');
        $billRegistryClientContractRepo=\Monkey::app()->repoFactory->create('BillRegistryContract');
        $billRegistryClientBillingInfoRepo = \Monkey::app()->repoFactory->create('BillRegistryClientBillingInfo');
        $country=\Monkey::app()->repoFactory->create('Country')->findAll();
        $shop=\Monkey::app()->repoFactory->create('Shop')->findAll();
        $userDetails=\Monkey::app()->repoFactory->create('UserDetails')->findAll();
        $brc=$billRegistryClientRepo->findOneBy(['id'=>$id]);
        $brca=$billRegistryClientAccountRepo->findOneBy(['billRegistryClientId'=>$id]);
        $billRegistryClientAccountId=$brca->id;
        $brp=$billRegistryGroupProduct->findAll();
        $brcbi=$billRegistryClientBillingInfoRepo->findOneBy(['billRegistryClientId'=>$id]);
        $brcl=$billRegistryClientLocationRepo->findBy(['billRegistryClientId'=>$id]);
        $brcc=$billRegistryClientContactRepo->findBy(['billRegistryClientId'=>$id]);
        $brcContract=$billRegistryClientContractRepo->findBy(['billRegistryClientId'=>$id,'billRegistryClientAccountId'=>$billRegistryClientAccountId]);
        $bankRegistry=\Monkey::app()->repoFactory->create('BankRegistry')->findBy(['id'=>$brcbi->bankRegistryId]);
        $currency=\Monkey::app()->repoFactory->create('Currency')->findBy(['id'=>$brcbi->currencyId]);
        $billRegistryTypePayment=\Monkey::app()->repoFactory->create('BillRegistryTypePayment')->findBy(['id'=>$brcbi->billRegistryTypePaymentId]);
        $billRegistryTypeTaxes=\Monkey::app()->repoFactory->create('BillRegistryTypeTaxes')->findBy(['id'=>$brcbi->billRegistryTypeTaxesId]);
        $typeFriend=\Monkey::app()->repoFactory->create('TypeFriend')->findAll();







        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'brc'=>$brc,
            'brca'=>$brca,
            'brcbi'=>$brcbi,
            'brcl'=>$brcl,
            'brcc'=>$brcc,
            'brp'=>$brp,
            'country'=>$country,
            'brcContract'=>$brcContract,
            'shop'=>$shop,
            'userDetails'=>$userDetails,
            'bankRegistry'=>$bankRegistry,
            'currency'=>$currency,
            'billRegistryTypePayment'=>$billRegistryTypePayment,
            'billRegistryTypeTaxes'=>$billRegistryTypeTaxes,
            'typeFriend'=>$typeFriend,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}