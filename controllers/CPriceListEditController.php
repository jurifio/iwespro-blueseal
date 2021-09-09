<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\exceptions\BambooException;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;
use PDO;
use PDOException;

/**
 * Class CPriceListEditController
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
class CPriceListEditController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "price_list_edit";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/price_list_edit.php');

        $priceId = $this->app->router->getMatchedRoute()->getComputedFilter('id');
        $shopId =$this->app->router->getMatchedRoute()->getComputedFilter('shopId');
        $priceList = \Monkey::app()->repoFactory->create('PriceList')->findOneBy(['id' => $priceId,'shopId'=>$shopId]);
        $shops  =  \Monkey::app()->repoFactory->create('Shop')->findAll();


        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'priceList' => $priceList,
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
            $priceListId = $data['id'];
            $shopId=$data['shopId'];
            $priceListRepo = \Monkey::app()->repoFactory->create('PriceList');
            $name=$data['name'];
            $variation=$data['variation'];
            $typeVariation=$data['typeVariation'];
            $variationSale=$data['variationSale'];
            $typeVariationSale=$data['typeVariationSale'];
            $dateStart = (new \DateTime($data['dateStart']))->format('Y-m-d H:i:s');
            $dateEnd=(new \DateTime($data['dateEnd']))->format('Y-m-d H:i:s');
            $priceList = $priceListRepo->findOneBy(['id'=> $priceListId,'shopId'=> $shopId]);
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

            $stmtUpdatePriceList=$db_con->prepare('update  PriceList set 
                    id='.$priceListId.',
                   shopId='.$shopId.',
                   `name`="'.$name.'",
                   typeVariation="'.$typeVariation.'",
                   typeVariationSale="'.$typeVariationSale.'",
                   variation="'.$variation.'",
                   variationSale="'.$variationSale.'",
                   dateStart="'.$dateStart.'",
                   dateEnd="'.$dateEnd.'" where id='.$priceListId.' and shopId='.$shopId);

            $stmtUpdatePriceList->execute();

            $priceList->id=$priceListId;
            $priceList->shopId=$shopId;
            $priceList->name=$name;
            $priceList->typeVariation=$typeVariation;
            $priceList->typeVariationSale=$typeVariationSale;
            $priceList->variation=$variation;
            $priceList->variationSale=$variationSale;
            $priceList->dateStart=$dateStart;
            $priceList->dateEnd=$dateEnd;
            $priceList->update();
            $link=$priceListId.'/'.$shopId.'/';

            return $link;
        } catch (\Throwable $e) {
            $this->app->router->response()->raiseProcessingError();
            $this->app->router->response()->sendHeaders();
        }
    }
}