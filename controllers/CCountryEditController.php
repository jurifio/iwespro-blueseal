<?php

namespace bamboo\blueseal\controllers;

use bamboo\ecommerce\views\VBase;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class CCountryEditController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 04/01/2022
 * @since 1.0
 */
class CCountryEditController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "country_edit";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath() . $this->app->cfg()->fetch('paths','blueseal') . '/template/country_edit.php');
        $countryId = $this->app->router->getMatchedRoute()->getComputedFilter('id');
        $country = \Monkey::app()->repoFactory->create('Country')->findOneBy(['id' => $countryId]);
        $currency=\Monkey::app()->repoFactory->create('Currency')->findAll();
        $lang=\Monkey::app()->repoFactory->create('Lang')->findAll();


        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'countryId' => $countryId,
            'country' => $country,
            'currency'=>$currency,
            'lang'=>$lang,
            'sidebar' => $this->sidebar->build()
        ]);
    }
    public function put()
    {
        $data = $this->app->router->request()->getRequestData();

        \Monkey::app()->repoFactory->beginTransaction();
        try {
            $country = \Monkey::app()->repoFactory->create('Country')->findOneBy(['id' => $data['countryId']]);
            $country->name=$data['name'];
            $country->capital=$data['capital'];
            $country->shippingCost=$data['shippingCost'];
            $country->freeShippingLimit=$data['freeShippingLimit'];
            $country->ISO=$data['ISO'];
            $country->ISO3=$data['ISO3'];
            $country->continent=$data['continent'];
            $country->tld=$data['tld'];
            $country->currencyDisplay=$data['currencyDisplay'];
            $country->currencyPayment=$data['currencyPayment'];
            $country->currencyCode=$data['currencyCode'];
            $country->currency=$data['currency'];
            $country->phone=$data['phone'];
            $country->postCodeFormat=$data['postCodeFormat'];
            $country->postCodeRegex=$data['postCodeRegex'];
            $country->langs=$data['langs'];
            $country->vat=$data['vat'];
            $country->extraUe=$data['extraUe'];
            $country->currentLang=$data['currentLang'];

            $country->update();

            \Monkey::app()->repoFactory->commit();
            return true;
        } catch(\Throwable $e){

            \Monkey::app()->repoFactory->rollback();
            return false;

        }
    }

}