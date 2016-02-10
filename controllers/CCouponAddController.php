<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\base\CSerialNumber;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

/**
 * Class CCouponAddController
 * @package redpanda\app\controllers
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
     * @throws \redpanda\core\exceptions\RedPandaORMException
     */
    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->cfg()->fetch('paths','blueseal').'/template/coupon_add.php');

        $em = $this->app->entityManagerFactory->create('CouponType');
        $couponTypes = $em->findAll('limit 9999');

        $serial = new CSerialNumber();
        $serial->generate();

        $today = new \DateTime();
        $expire = new \DateInterval('P1Y');
        $today->add($expire);

        echo $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'couponTypes' => $couponTypes,
            'couponCode' => $serial->__toString(),
            'validThru' => $today->format('Y-m-d\TH:i:s'),
            'page'=>$this->page,
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
            $couponRepo = $this->app->repoFactory->create('Coupon');
            $coupon = $couponRepo->getEmptyEntity();
            foreach ($data as $k => $v) {
                $coupon->{$k} = $v;
            }
            $date = new \DateTime();
            $coupon->issueDate = $date->format('Y-m-d H:i:s');
            $id = $couponRepo->insert($coupon);
            echo $id;
        } catch (\Exception $e) {
            $this->app->router->response()->raiseProcessingError();
            $this->app->router->response()->sendHeaders();
        }
    }
}