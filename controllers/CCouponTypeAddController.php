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
            $couponType->smartInsert();

            foreach ($data['tags'] ?? [] as $tag) {
                $couponTypeHasTag = \Monkey::app()->repoFactory->create('CouponTypeHasTag')->getEmptyEntity();
                $couponTypeHasTag->tagId = $tag;
                $couponTypeHasTag->couponTypeId = $couponType->id;
                $couponTypeHasTag->insert();
            }

            return $couponType->id;
        } catch (\Throwable $e) {
            $this->app->router->response()->raiseProcessingError();
            $this->app->router->response()->sendHeaders();
            return $e->getMessage()."\n".$e->getTraceAsString();
        }
    }
}