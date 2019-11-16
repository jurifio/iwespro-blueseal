<?php

namespace bamboo\blueseal\controllers;

use bamboo\core\base\CSerialNumber;
use bamboo\core\exceptions\BambooException;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;
use PDO;
use PDOException;

/**
 * Class CCouponTypeAddController
 * @package bamboo\app\controllers
 */
class CCouponTypeAddController extends ARestrictedAccessRootController
{
    /**
     * @var string
     */
    protected $fallBack = "blueseal";
    /**
     * @var string
     */
    protected $pageSlug = "coupontype_add";

    /**
     * @throws \bamboo\core\exceptions\RedPandaORMException
     */
    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath() . $this->app->cfg()->fetch('paths','blueseal') . '/template/coupontype_add.php');

        $possValids = [];
        $possValids[0] = '1 anno';
        $possValids[1] = '1 mese';
        $possValids[2] = '3 giorni';
        $possValids[3] = '7 giorni';
        $possValids[4] = '14 giorni';
        $possValids[5] = '21 giorni';

        $possValidity = [];
        $possValidity[0] = 'P1Y';
        $possValidity[1] = 'P1M';
        $possValidity[2] = 'P3D';
        $possValidity[3] = 'P7D';
        $possValidity[4] = 'P14D';
        $possValidity[5] = 'P21D';

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'possValids' => $possValids,
            'possValidity' => $possValidity,
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }

    /**
     * @return mixed
     */
    public function post()
    {
        try {
            $data = $this->app->router->request()->getRequestData();
            $couponType = \Monkey::app()->repoFactory->create('CouponType')->getEmptyEntity();
            $couponType->name = $data['name'];
            $couponType->amount = $data['amount'];
            $couponType->amountType = $data['amountType'];
            $couponType->validity = $data['validity'];
            $couponType->validForCartTotal = $data['validForCartTotal'];
            $couponType->hasFreeShipping = (isset($data['hasFreeShipping']) && $data['hasFreeShipping'] === 'on') ? 1 : 0;
            $couponType->hasFreeReturn = (isset($data['hasFreeReturn']) && $data['hasFreeReturn'] === 'on') ? 1 : 0;
            $coupnType->campaignId=$data['campaignId'];
            $findShopId = \Monkey::app()->repoFactory->create('Shop')->findOneBy(['id' => $orderLine->shopId]);
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
                $stmtCouponTypeInsert = $db_con->prepare('INSERT INTO CouponType (`name`,amount,amountType,validity,validForCartTotal,hasFreeShipping,hasFreeReturn,campaignId,isImportt)
                VALUES(
                                 \'' . $data['name'] . '\',
                                 \'' . $data['amount'] . '\',
                                 \'' . $data['amountType'] . '\',
                                 \'' . $data['validity'] . '\',
                                 \'' . $data['validForCartTotal'] . '\',
                                 \'' . $data['hasFreeShipping'] . '\',
                                 \'' . $data['hasFreeReturn'] . '\',
                                  \''. $data['campaignId'].'\',
                                 1                        
                                    )');
                $stmtCouponTypeInsert->exceute();
                $remoteId = $db_con->lastInsertId();
                $couponType->remoteShopId=$data['remoteShopId'];
                $couponType->remoteId=$remoteId;
                $couponType->smartInsert();

                foreach ($data['tags'] ?? [] as $tag) {
                    $couponTypeHasTag = \Monkey::app()->repoFactory->create('CouponTypeHasTag')->getEmptyEntity();
                    $couponTypeHasTag->tagId = $tag;
                    $couponTypeHasTag->couponTypeId = $couponType->id;
                    $couponTypeHasTag->insert();
                }
                return $couponType->id;
            }
        catch
            (\Throwable $e) {
                $this->app->router->response()->raiseProcessingError();
                $this->app->router->response()->sendHeaders();
                return $e->getMessage() . "\n" . $e->getTraceAsString();
            }
    }
}