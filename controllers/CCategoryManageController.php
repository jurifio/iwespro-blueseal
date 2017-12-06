<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\core\utils\slugify\CSlugify;
use bamboo\ecommerce\views\VBase;
use bamboo\core\db\pandaorm\entities\CEntityManager;
use bamboo\core\db\pandaorm\adapter\CMySQLAdapter;
use bamboo\core\base\CObjectCollection;

/**
 * Class CCategoryManageController
 * @package bamboo\blueseal\controllers
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CCategoryManageController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "product_category_add";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/category_edit.php');
        /** @var $em CEntityManager **/
        $categories = new CObjectCollection();
	    $categoryLang = null;
        if(isset($_GET['productCategoryId'])){
            $categoryLang = \Monkey::app()->repoFactory->create('ProductCategoryTranslation',false);
            $cat = $this->app->categoryManager->categories()->getDescendantsByNodeId($_GET['productCategoryId']);
            /** @var $em CEntityManager **/
            $em = $this->app->entityManagerFactory->create('ProductCategory',false);
            $em->setLang(null);


            foreach($cat as $val){
                $category = $em->findOne(array($val['id']));
                $category->depth = $val['depth'];
                $categories->add($category);
            }
        }
        $em = $this->app->entityManagerFactory->create('Lang');
        $langs = $em->findAll("","");

        $blueseal = $this->app->baseUrl().'/blueseal';
        $elenco_prodotti = $blueseal."/prodotti";
        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'categories' => $categories,
            'categoryLang'=>$categoryLang,
            'langs' => $langs,
            'elenco_prodotti' =>$elenco_prodotti,
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }

    public function post()
    {
        $blueseal = $this->app->baseUrl(false).'/blueseal';
        $post= $_POST;
        $productCategoryId = $this->app->router->request()->getRequestData('productCategoryId');

        /** @var CMySQLAdapter $mysql */
        $mysql = $this->app->dbAdapter;

        $id = 0;

        $pcRepo = \Monkey::app()->repoFactory->create('ProductCategory');
        $pctRepo = \Monkey::app()->repoFactory->create('ProductCategoryTranslation');
        $slugy = new CSlugify();

        foreach($post as $key => $input){
            if ($key == 'productCategoryId') continue;
            $temp = explode('_' ,$key);
            if($temp[0] != 'cat') continue;
            if($temp[2] == 'slug') {
                $pc = $pcRepo->findOneBy(['id' => $temp[1]]);
                $pc->slug = $slugy->slugify(trim($input));
                $pc->update();
                continue;
            }
            $id = $temp[1];
            $pct = $pctRepo->findOneBy(["productCategoryId"=>$temp[1],"langId"=>$temp[2]]);
            if($pct) {
                if($pct->name != $input){
                    $pct->name = $input;
                    $pct->update();
                }
            } else if(!empty($input)){
                $pct = $pctRepo->getEmptyEntity();
                $pct->productCategoryId = $temp[1];
                $pct->langId = $temp[2];
                $pct->slug = $slugy->slugify(trim($input));
                $mysql->insert("ProductCategoryTranslation", array("productCategoryId"=>$temp[1],"langId"=>$temp[2],'name'=>$input));
            }
        }

        return $this->get();
    }
}