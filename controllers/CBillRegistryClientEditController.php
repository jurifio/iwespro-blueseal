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
        $billRegistryClientAccountHasProductRepo = \Monkey::app()->repoFactory->create('BillRegistryClientAccountHasProduct');
        $billRegistryClientLocationRepo= \Monkey::app()->repoFactory->create('BillRegistryClientLocation');
        $billRegistryClientContactRepo=\Monkey::app()->repoFactory->create('BillRegistryClientContact');
        $billRegistryClientContractRepo=\Monkey::app()->repoFactory->create('BillRegistryClientContract');
        $billRegistryClientBillingInfoRepo = \Monkey::app()->repoFactory->create('BillRegistryClientBillingInfo');
        $country=\Monkey::app()->repoFactory->create('Country')->findAll();
        $shop=\Monkey::app()->repoFactory->create('Shop')->findAll();
        $userDetails=\Monkey::app()->repoFactory->create('UserDetails')->findAll();
        $brc=$billRegistryClientRepo->findOneBy(['id'=>$id]);
        $brca=$billRegistryClientAccountRepo->findOneBy(['billRegistryClientId'=>$id]);
        $billRegistryClientAccountId=$brca->id;
        $brcahp=$billRegistryClientAccountHasProductRepo->findBy(['billRegistryClientAccountId'=>$billRegistryClientAccountId]);
        $brcbi=$billRegistryClientBillingInfoRepo->findOneBy(['billRegistryClientId'=>$id]);
        $brcl=$billRegistryClientLocationRepo->findBy(['billRegistryClientId'=>$id]);
        $brcc=$billRegistryClientContactRepo->findBy(['billRegistryClientId'=>$id]);
        $brcContract=$billRegistryClientContractRepo->findBy(['billRegistryClientId'=>$id],['billRegistryClientAccountId'=>$billRegistryClientAccountId]);
        $bankRegistry=\Monkey::app()->repoFactory->create('BankRegistry')->findAll();
        $currency=\Monkey::app()->repoFactory->create('Currency')->findAll();
        $billRegistryTypePayment=\Monkey::app()->repoFactory->create('BillRegistryTypePayment')->findAll();
        $billRegistryTypeTaxes=\Monkey::app()->repoFactory->create('BillRegistryTypeTaxes')->findAll();
        $typeFriend=\Monkey::app()->repoFactory->create('TypeFriend')->findAll();







        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'brc'=>$brc,
            'brca'=>$brca,
            'brcahp'=>$brcahp,
            'brcbi'=>$brcbi,
            'brcl'=>$brcl,
            'brcc'=>$brcc,
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