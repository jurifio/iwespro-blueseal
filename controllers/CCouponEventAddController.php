<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\base\CSerialNumber;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

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
     * @return void
     */
    public function post()
    {
        try {
            $data = $this->app->router->request()->getRequestData();
            $couponRepo = $this->app->repoFactory->create('CouponEvent');
            $coupon = $couponRepo->getEmptyEntity();
            foreach ($data as $k => $v) {
                $coupon->{$k} = $v;
            }
            $id = $couponRepo->insert($coupon);
            echo $id;
        } catch (\Exception $e) {
            $this->app->router->response()->raiseProcessingError();
            $this->app->router->response()->sendHeaders();
        }
    }
}