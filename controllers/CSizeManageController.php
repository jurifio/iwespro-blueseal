<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;
use bamboo\core\db\pandaorm\entities\CEntityManager;
use bamboo\core\db\pandaorm\adapter\CMySQLAdapter;
use bamboo\core\utils\slugify\CSlugify;

/**
 * Class CSizeManageController
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
class CSizeManageController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "product_sizegroup_add";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/sizegroup_add.php');
        /** @var $em CEntityManager **/
        $sizeEdit = null;
        if(isset($_GET['productSizeGroupId'])){
            $em = $this->app->entityManagerFactory->create('ProductSizeGroup');
            $sizeEdit = $em->findBySql("select id  from ProductSizeGroup where `macroName` = (select `macroName` from ProductSizeGroup where id = ?)",array($_GET['productSizeGroupId']));
        }

        echo $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'sizeEdit' => $sizeEdit,
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }

    public function post()
    {
        $post = $this->app->router->request()->getRequestData();

        foreach($post as $key =>$val){
            if(empty($val) && $val != '0') unset($post[$key]);
        }
        $slugy = new CSlugify();

        $blueseal = $this->app->baseUrl(false).'/blueseal/';
        $this->app->dbAdapter->beginTransaction();
        /** @var CMySQLAdapter $mysql */
        $mysql = $this->app->dbAdapter;
        $macroName = $post['ProductSizeGroup_macroName'];
        for($k=0;$k<12;$k++){
            $productSizeGroupIn = [];
            $positions = [];
            $productSizeGroupId = "";
            $keys = [];
            foreach($post as $key=>$val){
                $keys = explode('_', $key);
                if(!strstr($key, 'ProductSizeGroup_'.$k)) continue;
                if($keys[2] == 'position') {
                    $positions[$keys[3]] = $val;
                    continue;
                };
                if($keys[2] == 'id') {
                    $productSizeGroupId = $val;
                    continue;
                };
                $productSizeGroupIn[$keys[2]] = $val;
            }

            try{
                if(empty($productSizeGroupIn) || empty($positions)) continue;
                $productSizeGroupIn['macroName'] = $macroName;
                if(empty($productSizeGroupId)){
                    $productSizeGroupId = $mysql->insert("ProductSizeGroup",$productSizeGroupIn);
                }else{
                    if(empty($positions)){
                        $mysql->delete("ProductSizeGroupHasProductSize", ["productSizeGroupId"=>$productSizeGroupId]);
                        $mysql->delete("ProductSizeGroup",["id"=>$productSizeGroupId]);
                        continue;
                    }

                    $productSizeGroup = $this->app->repoFactory->create("ProductSizeGroup")->findOneBy(['id' => $productSizeGroupId]);
                    $productSizeGroup->macroName = $macroName;
                    $productSizeGroup->locale = $productSizeGroupIn['locale'];
                    $productSizeGroup->name = $productSizeGroupIn['name'];
                    $productSizeGroup->update();
                    //$mysql->update ("ProductSizeGroup",$productSizeGroupIn,array("id"=>$productSizeGroupId));
                }
            }catch(\Exception $e){
                $mysql->rollback();
                /** @noinspection PhpVoidFunctionResultUsedInspection */
                die(var_dump($e));
            }

            try{
                $res = $mysql->delete("ProductSizeGroupHasProductSize", ["productSizeGroupId"=>$productSizeGroupId]);
                foreach($positions as $key=>$val){
                    if($sizeId = $mysql->query("select id from ProductSize where name = ?",[$val])->fetch()){
                        $sizeId = $sizeId['id'];
                    } else {
                        $slug = str_replace('½','m',$val);
                        $slug = $slugy->slugify($slug);
                        $sizeId = $mysql->insert("ProductSize", ["name"=>$val, "slug"=>$slug]);
                    }
                    $res = $mysql->insert("ProductSizeGroupHasProductSize", ["productSizeGroupId"=>$productSizeGroupId,"productSizeId"=>$sizeId,"position"=>$key]);
                }
            }catch(\Exception $e){
                $this->app->dbAdapter->rollback();
                /** @noinspection PhpVoidFunctionResultUsedInspection */
                die(var_dump($e));
            }
        }
        $mysql->commit();

        return $this->get();
    }
}