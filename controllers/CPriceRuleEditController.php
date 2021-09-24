<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\exceptions\BambooException;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;
use PDO;
use PDOException;

/**
 * Class CPriceRuleEditController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 15/09/2021
 * @since 1.0
 */
class CPriceRuleEditController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "price_rule_edit";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/price_rule_edit.php');

        $priceId = $this->app->router->getMatchedRoute()->getComputedFilter('id');
        $shopId =$this->app->router->getMatchedRoute()->getComputedFilter('shopId');
        $priceRuleList = \Monkey::app()->repoFactory->create('PriceRuleList')->findOneBy(['id' => $priceId,'shopId'=>$shopId]);
        $shops  =  \Monkey::app()->repoFactory->create('Shop')->findAll();


        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'priceRuleList' => $priceRuleList,
            'priceId'=>$priceId,
            'shops' =>$shops,
            'shopId'=>$shopId,
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }

    public function put()
    {

        try {
            $data = $this->app->router->request()->getRequestData();
            $priceRuleId = $data['priceRuleId'];
            $shopId=$data['shopId'];
            $priceRuleListRepo = \Monkey::app()->repoFactory->create('PriceRuleList');
            $name=$data['name'];
            $seasonsPar=$data['seasonsPar'];
            $brandsPar=$data['brandsPar'];
            $variation=$data['variation'];
            $typeVariation=$data['typeVariation'];
            $typeAssignBrand=$data['typeAssignBrand'];
            $typeAssignSeason=$data['typeAssignSeason'];
            $optRadioRound=$data['optRadioRound'];
            $variationSale=$data['variationSale'];
            $typeVariationSale=$data['typeVariationSale'];
            $dateStart = (new \DateTime($data['dateStart']))->format('Y-m-d H:i:s');
            $dateEnd=(new \DateTime($data['dateEnd']))->format('Y-m-d H:i:s');
            $priceRuleList = $priceRuleListRepo->findOneBy(['id'=> $priceRuleId,'shopId'=> $shopId]);
            $shopId=$data['shopId'];
            $findShopId = \Monkey::app()->repoFactory->create('Shop')->findOneBy(['id' => $shopId]);
            $db_host = $findShopId->dbHost;
            $db_name = $findShopId->dbName;
            $db_user = $findShopId->dbUsername;
            $db_pass = $findShopId->dbPassword;
            try {
                $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
                $db_con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                $res = " connessione ok <br>";
            } catch (PDOException $e) {
                throw new BambooException('fail to connect');

            }

            $stmtUpdatePriceRuleList=$db_con->prepare('update  PriceRuleList set 
                    id='.$priceRuleId.',
                   shopId='.$shopId.',
                   `name`="'.$name.'",
                   typeVariation="'.$typeVariation.'",
                   typeVariationSale="'.$typeVariationSale.'",
                   variation="'.$variation.'",
                   variationSale="'.$variationSale.'",
                    optRadioRound="'.$optRadioRound.'",
                    typeAssignBrand="'.$typeAssignBrand.'",
                    brandsPar="'.$brandsPar.'",
                    typeAssignSeason="'.$typeAssignSeason.'",
                    seasonsPar="'.$seasonsPar.'",
                   dateStart="'.$dateStart.'",
                   dateEnd="'.$dateEnd.'" where id='.$priceRuleId.' and shopId='.$shopId);

            $stmtUpdatePriceRuleList->execute();

            $priceRuleList->id=$priceRuleId;
            $priceRuleList->shopId=$shopId;
            $priceRuleList->name=$name;
            $priceRuleList->typeVariation=$typeVariation;
            $priceRuleList->typeVariationSale=$typeVariationSale;
            $priceRuleList->variation=$variation;
            $priceRuleList->variationSale=$variationSale;
            $priceRuleList->optRadioRound=$optRadioRound;
            $priceRuleList->typeAssignBrand=$typeAssignBrand;
            $priceRuleList->brandsPar=$brandsPar;
            $priceRuleList->typeAssignSeason=$typeAssignSeason;
            $priceRuleList->seasonsPar=$seasonsPar;
            $priceRuleList->dateStart=$dateStart;
            $priceRuleList->dateEnd=$dateEnd;
            $priceRuleList->update();
            $link=$priceListId.'/'.$shopId.'/';

            return $link;
        } catch (\Throwable $e) {
            $this->app->router->response()->raiseProcessingError();
            $this->app->router->response()->sendHeaders();
        }
    }
}