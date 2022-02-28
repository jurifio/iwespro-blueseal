<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\exceptions\BambooException;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;
use PDO;
use PDOException;

/**
 * Class CPriceListAddController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 08/09/2021
 * @since 1.0
 */
class CPriceListAddController extends ARestrictedAccessRootController
{
    /**
     * @var string
     */
    protected $fallBack = "blueseal";
    /**
     * @var string
     */
    protected $pageSlug = "price_list_add";


    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/price_list_add.php');

        $langs = \Monkey::app()->repoFactory->create('Lang')->findBy(['isActive'=>1]);
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
            $priceListRepo = \Monkey::app()->repoFactory->create('PriceList');
            $name=$data['name'];
            $variation=$data['variation'];
            $typeVariation=$data['typeVariation'];
            $variationSale=$data['variationSale'];
            $typeVariationSale=$data['typeVariationSale'];
            $dateStart = (new \DateTime($data['dateStart']))->format('Y-m-d H:i:s');
            $dateEnd=(new \DateTime($data['dateEnd']))->format('Y-m-d H:i:s');
            $priceList = $priceListRepo->getEmptyEntity();
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
            $stmtGetLastId=$db_con->prepare('select (max(id) +1) as newId from PriceList where shopId='.$shopId);
            $stmtGetLastId->execute();
                while ($rowGetLastId = $stmtGetLastId -> fetch(PDO::FETCH_ASSOC)) {
                    $lastId=$rowGetLastId['newId'];
                }
                $stmtInsertPriceList=$db_con->prepare('INSERT INTO PriceList  (id, shopId,`name`,typeVariation,typeVariationSale,variation,variationSale,dateStart,dateEnd)
                value  (
                        '.$lastId.',
                        '.$shopId.',
                        "'.$name.'",
                        '.$typeVariation.',
                        '.$typeVariationSale.',
                        '.$variation.',
                        '.$variationSale.',
                        "'.$dateStart.'",
                        "'.$dateEnd.'")');
            $stmtInsertPriceList->execute();

            $priceList->id=$lastId;
            $priceList->shopId=$shopId;
            $priceList->name=$name;
            $priceList->typeVariation=$typeVariation;
            $priceList->typeVariationSale=$typeVariationSale;
            $priceList->variation=$variation;
            $priceList->variationSale=$variationSale;
            $priceList->dateStart=$dateStart;
            $priceList->dateEnd=$dateEnd;
            $priceList->insert();
                $link=$lastId.'/'.$shopId.'/';

            return $link;
        } catch (\Throwable $e) {
            $this->app->router->response()->raiseProcessingError();
            $this->app->router->response()->sendHeaders();
        }
    }
}