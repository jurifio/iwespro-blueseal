<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\exceptions\BambooException;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;
use PDO;
use PDOException;

/**
 * Class CPriceRuleAddController
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
class CPriceRuleAddController extends ARestrictedAccessRootController
{
    /**
     * @var string
     */
    protected $fallBack = "blueseal";
    /**
     * @var string
     */
    protected $pageSlug = "price_rule_add";


    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/price_rule_add.php');

        $langs = \Monkey::app()->repoFactory->create('Lang')->findAll();
        $shops  =  \Monkey::app()->repoFactory->create('Shop')->findAll();


        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'langs' => $langs,
            'page'=>$this->page,
            'shops'=>$shops,
            'sidebar'=>$this->sidebar->build()
        ]);
    }

    /**
     * @return void
     */
    public function post()
    {
        try {
            $data = $this->app->router->request()->getRequestData();
            $priceRuleRepo = \Monkey::app()->repoFactory->create('PriceRuleList');
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
            $priceRuleList = $priceRuleRepo->getEmptyEntity();
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
            $stmtGetLastPriceRuleId=$db_con->prepare('select (max(id) +1) as newId from PriceRuleList where shopId='.$shopId);
            $stmtGetLastPriceRuleId->execute();
                while ($rowGetLastPriceRuleId = $stmtGetLastPriceRuleId -> fetch(PDO::FETCH_ASSOC)) {
                    $lastId=$rowGetLastPriceRuleId['newId'];
                }
                if($lastId==null){
                    $lastId=1;
                }
                $stmtInsertPriceRuleList=$db_con->prepare('INSERT INTO PriceRuleList  (
                            id, 
                            shopId,
                            `name`,
                            typeVariation,
                            typeVariationSale,
                            variation,
                            variationSale,
                            optRadioRound,
                            typeAssignBrand,
                            brandsPar,
                            typeAssignSeason,
                            seasonsPar,
                            dateStart,
                            dateEnd)
                value  (
                        '.$lastId.',
                        '.$shopId.',
                        "'.$name.'",
                        '.$typeVariation.',
                        '.$typeVariationSale.',
                        '.$variation.',
                        '.$variationSale.',
                        "'.$optRadioRound.'",
                        "'.$typeAssignBrand.'",
                        "'.$brandsPar.'",
                        "'.$typeAssignSeason.'",
                        "'.$seasonsPar.'",
                        "'.$dateStart.'",
                        "'.$dateEnd.'")');
            $stmtInsertPriceRuleList->execute();

            $priceRuleList->id=$lastId;
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
            $priceRuleList->insert();
                $link=$lastId.'/'.$shopId.'/';

            return $link;
        } catch (\Throwable $e) {
            $this->app->router->response()->raiseProcessingError();
            $this->app->router->response()->sendHeaders();
        }
    }
}