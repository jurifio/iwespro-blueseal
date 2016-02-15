<?php
namespace bamboo\blueseal\controllers

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;
/**
 * Class CBrandEditController
 * @package bamboo\app\controllers
 */
class CBrandEditController extends CBrandManageController
{
    /**
     * @var string
     */
    protected $fallBack = "blueseal";
    /**
     * @var string
     */
    protected $pageSlug = "product_brand_edit";

    /**
     * @throws \bamboo\core\exceptions\RedPandaORMException
     */
    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->cfg()->fetch('paths','blueseal').'/template/brand_edit.php');

        $brandId =  $this->app->router->request()->getRequestData();
        $brandEdit = $this->app->repoFactory->create('ProductBrand')->findOneBy(['id'=>$brandId]);

        echo $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'brandEdit' => $brandEdit,
            'page'=>$this->page,
            'sidebar'=>$this->sidebar->build()
        ]);
    }
}