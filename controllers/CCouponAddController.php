<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\base\CSerialNumber;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;
use bamboo\utils\time\STimeToolbox;

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
            $coupon->amountType=$couponType->amountType;
            $coupon->userId = isset($data['userId']) && !empty($data['userId']) ? $data['userId'] : null;
            $coupon->valid = true;

            return $coupon->insert();
        } catch (\Throwable $e) {
            $this->app->router->response()->raiseProcessingError();
            $this->app->router->response()->sendHeaders();
            return $e->getTraceAsString();
        }
    }
}