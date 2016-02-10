<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

/**
 * Class CBrandAddController
 * @package redpanda\app\controllers
 */
class CBrandAddController extends CBrandManageController
{
    /**
     * @var string
     */
    protected $fallBack = "blueseal";
    /**
     * @var string
     */
    protected $pageSlug = "product_brand_add";

    /**
     * @throws \redpanda\core\exceptions\RedPandaORMException
     */
    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->cfg()->fetch('paths','blueseal').'/template/brand_add.php');

        $em = $this->app->entityManagerFactory->create('ProductBrand');

        $brandEdit = null;
        if (isset($_GET['productBrandId'])) {
            $brandEdit = $em->findOne([$_GET['productBrandId']]);
        }

        echo $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'brandEdit' => $brandEdit,
            'page'=>$this->page,
            'sidebar'=>$this->sidebar->build()
        ]);
    }
}