<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\base\CSerialNumber;
use bamboo\core\exceptions\BambooException;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;
use bamboo\utils\price\SPriceToolbox;
use bamboo\utils\time\STimeToolbox;

/**
 * Class CCouponEventAddController
 * @package bamboo\app\controllers
 */
class CCouponEventAddController extends ARestrictedAccessRootController
{
    /**
     * @var string
     */
    protected $fallBack = "blueseal";
    /**
     * @var string
     */
    protected $pageSlug = "couponevent_add";

    /**
     * @throws \bamboo\core\exceptions\RedPandaORMException
     */
    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/couponevent_add.php');

        $em = $this->app->entityManagerFactory->create('CouponType');
        $couponTypes = $em->findAll('limit 9999');

        $today = new \DateTime();
        $end = new \DateTime();
        $expire = new \DateInterval('P1Y');
        $end->add($expire);

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'couponTypes' => $couponTypes,
            'startDate' => $today->format('Y-m-d\TH:i:s'),
            'endDate' => $end->format('Y-m-d\TH:i:s'),
            'page'=>$this->page,
            'sidebar'=>$this->sidebar->build()
        ]);
    }

    /**
     * @return int
     */
    public function post()
    {
        try {
            $data = $this->app->router->request()->getRequestData();

            $couponEvent = \Monkey::app()->repoFactory->create('CouponEvent')->getEmptyEntity();
            $couponEvent->couponTypeId = $data['couponTypeId'];
            $couponEvent->name = $data['name'];
            $couponEvent->description = $data['description'];
            $couponEvent->startDate = STimeToolbox::DbFormattedDateTime($data['startDate']);
            $couponEvent->endDate = STimeToolbox::DbFormattedDateTime($data['endDate']);
            $couponEvent->remoteShopId=$data['remoteShopId'];
            $couponEvent->isCatalogue=$data['isCatalogue'];
            $couponEvent->isAnnounce=$data['isAnnounce'];
            $couponEvent->rowCataloguePosition=$data['rowCataloguePosition'];
            $couponEvent->couponText=$data['couponText'];
            $couponType=\Monkey::app()->repoFactory->create('CouponType')->findoneBy(['id'=>$couponEvent->couponTypeId,'remoteShopId'=>$data->remoteShopId]);
            $remoteCouponTypeId=$couponType->remoteId;
            $findShopId = \Monkey::app()->repoFactory->create('Shop')->findOneBy(['id' => $data['remoteShopId']]);
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
            $stmtCouponEventInsert = $db_con->prepare('INSERT INTO CouponEvent (couponTypeId,`name`,`description`,click,startDate,endDate,isCatalogue,isAnnounce,rowCataloguePosition,couponText,isImport)
                VALUES(
                                 \'' . $remoteCouponTypeId . '\',
                                 \'' . $data['name'] . '\',
                                 \'' . $data['description'] . '\',
                                 \'' . $data['click'] . '\',
                                 \'' . $data['startDate'] . '\',
                                 \'' . $data['endDate'] . '\',
                                 \'' . $data['isCatalogue'] . '\',
                                  \''. $data['isAnnounce'].'\',
                                  \''. $data['rowCataloguePosition'].'\',
                                  \''. $data['couponText'].'\',
                                 1                        
                                    )');
            $stmtCouponEventInsert->execute();
            $remoteEventId = $db_con->lastInsertId();
            $couponEvent->remoteId=$remoteEventId;

            return $couponEvent->insert();
        } catch (\Throwable $e) {
            $this->app->router->response()->raiseProcessingError();
            $this->app->router->response()->sendHeaders();
        }
    }
}