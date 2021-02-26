<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\base\CSerialNumber;
use bamboo\core\exceptions\BambooException;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;
use bamboo\utils\time\STimeToolbox;
use PDO;
use PDOException;

/**
 * Class CCouponAddController
 * @package bamboo\app\controllers
 */
class CCouponAddController extends ARestrictedAccessRootController
{
    /**
     * @var string
     */
    protected $fallBack = "blueseal";
    /**
     * @var string
     */
    protected $pageSlug = "coupon_add";

    /**
     * @throws \bamboo\core\exceptions\RedPandaORMException
     */
    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/coupon_add.php');

        $em = $this->app->entityManagerFactory->create('CouponType');
        $couponTypes = $em->findAll('limit 9999');

        $serial = new CSerialNumber();
        $serial->generate();

        $today = new \DateTime();
        $expire = new \DateInterval('P1Y');
        $today->add($expire);

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'couponTypes' => $couponTypes,
            'couponCode' => $serial->__toString(),
            'validThru' => $today->format('Y-m-d\TH:i:s'),
            'page'=>$this->page,
            'sidebar'=>$this->sidebar->build()
        ]);
    }

    /**
     * @return int|string
     */
    public function post()
    {
        try {
            $data = $this->app->router->request()->getRequestData();
            $couponRepo = \Monkey::app()->repoFactory->create('Coupon');
            $coupon = $couponRepo->getEmptyEntity();

            $coupon->couponTypeId = $data['couponTypeId'];
            $coupon->code = $data['code'];
            $coupon->issueDate = STimeToolbox::DbFormattedDateTime();
            $coupon->validThru = STimeToolbox::DbFormattedDateTime($data['validThru']);
            $coupon->amount = $data['amount'];
            $couponType=\Monkey::app()->repoFactory->create('CouponType')->findOneBy(['id'=>$data['couponTypeId']]);
            $remoteCouponTypeId=$couponType->remoteId;
            $coupon->amountType=$couponType->amountType;
            $coupon->userId = isset($data['userId']) && !empty($data['userId']) ? $data['userId'] : null;
            $coupon->remoteShopId=$data['remoteShopId'];
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
            if(isset($data['userId'])){
                $user=\Monkey::app()->repoFactory->create('User')->findOneBy(['id'=>$data['userId']]);
                $email=$user->email;
                $stmtUser=$db_con->prepare("select id from User where `email` like '%".$email."%'");
                $stmtUser->execute();
                while ($rowUser = $stmtUser -> fetch(PDO::FETCH_ASSOC)) {
                  $remoteUserId=  $rowUser['id'];
                }
            }else{
                $remoteUserId='null';
            }

            $coupon->valid = true;

            if($data['remoteShopId']==1) {
                $stmtCouponInsert = $db_con->prepare('INSERT INTO Coupon (couponTypeId,tagId,`code`,`issueDate`,`validhThru`,amount,amountType,userId,valid,couponEventId,sid,isImport,isExtended)
                VALUES(
                                 \'' . $remoteCouponTypeId . '\',
                                 null,
                                 \'' . $data['code'] . '\',
                                 \'' . STimeToolbox::DbFormattedDateTime() . '\',
                                 \'' . STimeToolbox::DbFormattedDateTime($data['validThru']) . '\',
                                 \'' . $data['amount'] . '\',
                                 \'' . $data['amountType'] . '\',
                                  ' . $remoteUserId. ',
                                  ' . 1 . ',
                                 null,
                                 null,
                                 null,                        
                                 null  )');
            }else{
                $stmtCouponInsert = $db_con->prepare('INSERT INTO Coupon (couponTypeId,`code`,`issueDate`,`validhThru`,amount,amountType,userId,valid,couponEventId,sid,isImport,isExtended)
                VALUES(
                                 \'' . $remoteCouponTypeId . '\',
                                 \'' . $data['code'] . '\',
                                  \'' . STimeToolbox::DbFormattedDateTime() . '\',
                                 \'' . STimeToolbox::DbFormattedDateTime($data['validThru']) . '\',
                                 \'' . $data['amount'] . '\',
                                 \'' . $data['amountType'] . '\',
                                  ' . $remoteUserId. ',
                                  ' . 1 . ',
                                 null,
                                 null,
                                 null,                        
                                 null  )');
            }
            $stmtCouponInsert->execute();
            $remoteId = $db_con->lastInsertId();
            $coupon->remoteId=$remoteId;

            return $coupon->insert();
        } catch (\Throwable $e) {
            $this->app->router->response()->raiseProcessingError();
            $this->app->router->response()->sendHeaders();
            return $e->getTraceAsString();
        }
    }
}