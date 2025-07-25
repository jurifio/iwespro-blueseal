<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

/**
 * Class CBrandAddController
 * @package bamboo\app\controllers
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
     * @throws \bamboo\core\exceptions\RedPandaORMException
     */
    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/brand_add.php');

        $em = $this->app->entityManagerFactory->create('ProductBrand');
        $currentUser=$this->app->getUser()->getId();
        if ($this->app->getUser()->hasPermission('allShops')) {
            $allShops='1';
        }else{
            $allShops='2';
        }
        $brandEdit = null;
        if (isset($_GET['productBrandId'])) {
            $brandEdit = $em->findOne([$_GET['productBrandId']]);
        }
        $langs=\Monkey::app()->repoFactory->create('Lang')->findBy(['isActive'=>1]);

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'brandEdit' => $brandEdit,
            'page'=>$this->page,
            'langs'=>$langs,
            'currentUser'=>$currentUser,
            'allShops'=>$allShops,
            'sidebar'=>$this->sidebar->build()
        ]);
    }
}