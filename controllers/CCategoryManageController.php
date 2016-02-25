<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
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
        if(isset($_GET['productCategoryId'])){
            $categoryLang = $this->app->repoFactory->create('ProductCategoryHasLang');
            $cat = $this->app->categoryManager->categories()->getDescendantsByNodeId($_GET['productCategoryId']);
            /** @var $em CEntityManager **/
            $em = $this->app->entityManagerFactory->create('ProductCategory');
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
        echo $view->render([
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
        /** @var CMySQLAdapter $mysql */
        $mysql = $this->app->dbAdapter;

        $id = 0;
        foreach($post as $key => $input){
            $temp = explode('_' ,$key);
            if($temp[0] != 'cat') continue;
            if($temp[2] == 'slug') {
                $mysql->update("ProductCategory",array('slug'=>trim($input)), array("id"=>$temp[1]));
                continue;
            }
            $id = $temp[1];
            $dbver = $mysql->select('ProductCategoryHasLang',array("productCategoryId"=>$temp[1],"langId"=>$temp[2]))->fetch();
            if(isset($dbver['name'])) {
                if($dbver['name'] != $input){
                    $mysql->update("ProductCategoryHasLang",array('name'=>trim($input)), array("productCategoryId"=>$temp[1],"langId"=>$temp[2]));
                }
            } else if(!empty($input)){
                $mysql->insert("ProductCategoryHasLang", array("productCategoryId"=>$temp[1],"langId"=>$temp[2],'name'=>$input));
            }
        }

        if(!headers_sent()){
            header("Location: ".$blueseal."/categories");
        }
    }
}