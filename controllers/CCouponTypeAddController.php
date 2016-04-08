<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\base\CSerialNumber;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

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
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/coupontype_add.php');

        $possValids =[];
        $possValids[0] = '1 anno';
        $possValids[1] = '1 mese';
        $possValids[2] = '7 giorni';

        $possValidity = [];
        $possValidity[0] = 'P1Y';
        $possValidity[1] = 'P1M';
        $possValidity[2] = 'P7D';

         return $view->render([
             'app' => new CRestrictedAccessWidgetHelper($this->app),
             'possValids' => $possValids,
             'possValidity' => $possValidity,
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
            $couponRepo = $this->app->repoFactory->create('CouponType');
            $coupon = $couponRepo->getEmptyEntity();
            foreach ($data as $k => $v) {
                $coupon->{$k} = $v;
            }
            $id = $couponRepo->insert($coupon);
            return $id;
        } catch (\Exception $e) {
            $this->app->router->response()->raiseProcessingError();
            $this->app->router->response()->sendHeaders();
        }
    }
}