<?php

namespace bamboo\blueseal\controllers;

use bamboo\core\db\pandaorm\adapter\CMySQLAdapter;
use bamboo\core\db\pandaorm\entities\CEntityManager;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\core\utils\slugify\CSlugify;
use bamboo\ecommerce\views\VBase;

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

    public function post()
    {
        $post = $this->app->router->request()->getRequestData();

        foreach ($post as $key => $val) {
            if (empty($val) && $val != '0') unset($post[$key]);
        }
        $slugy = new CSlugify();

        $blueseal = $this->app->baseUrl(false) . '/blueseal/';
        \Monkey::app()->repoFactory->beginTransaction();
        /** @var CMySQLAdapter $mysql */
        $mysql = $this->app->dbAdapter;
        $macroName = $post['ProductSizeGroup_macroName'];
        $productSizeMacroGroup = \Monkey::app()->repoFactory->create('ProductSizeMacroGroup')->findOneBy(['name' => $macroName]);
        for ($k = 0; $k < 24; $k++) {
            $productSizeGroupIn = [];
            $positions = [];
            $productSizeGroupId = "";
            $keys = [];
            foreach ($post as $key => $val) {
                $keys = explode('_', $key);
                if (!strstr($key, 'ProductSizeGroup_' . $k)) continue;
                if ($keys[2] == 'position') {
                    $positions[$keys[3]] = $val;
                    continue;
                };
                if ($keys[2] == 'id') {
                    $productSizeGroupId = $val;
                    continue;
                };
                $productSizeGroupIn[$keys[2]] = $val;
            }

            try {
                if (empty($productSizeGroupIn) || empty($positions)) continue;
                $productSizeGroupIn['productSizeMacroGroupId'] = $productSizeMacroGroup->id;
                if (empty($productSizeGroupId)) {
                    $productSizeGroupId = $mysql->insert("ProductSizeGroup", $productSizeGroupIn);
                } else {
                    if (empty($positions)) {
                        $mysql->delete("ProductSizeGroupHasProductSize", ["productSizeGroupId" => $productSizeGroupId]);
                        $mysql->delete("ProductSizeGroup", ["id" => $productSizeGroupId]);
                        continue;
                    }

                    $productSizeGroup = \Monkey::app()->repoFactory->create("ProductSizeGroup")->findOneBy(['id' => $productSizeGroupId]);
                    $productSizeGroup->productSizeMacroGroupId = $productSizeMacroGroup->id;
                    $productSizeGroup->locale = $productSizeGroupIn['locale'];
                    $productSizeGroup->name = $productSizeGroupIn['name'];
                    $productSizeGroup->update();
                    //$mysql->update ("ProductSizeGroup",$productSizeGroupIn,array("id"=>$productSizeGroupId));
                }
            } catch (\Throwable $e) {
                $mysql->rollback();
                /** @noinspection PhpVoidFunctionResultUsedInspection */
                die(var_dump($e));
            }

            try {
                $res = $mysql->delete("ProductSizeGroupHasProductSize", ["productSizeGroupId" => $productSizeGroupId]);
                foreach ($positions as $key => $val) {
                    if ($sizeId = $mysql->query("SELECT id FROM ProductSize WHERE name = ?", [$val])->fetch()) {
                        $sizeId = $sizeId['id'];
                    } else {
                        $slug = str_replace('½', 'm', $val);
                        $slug = $slugy->slugify($slug);
                        $sizeId = $mysql->insert("ProductSize", ["name" => $val, "slug" => $slug]);
                    }
                    $res = $mysql->insert("ProductSizeGroupHasProductSize", ["productSizeGroupId" => $productSizeGroupId, "productSizeId" => $sizeId, "position" => $key]);
                }
            } catch (\Throwable $e) {
                \Monkey::app()->repoFactory->rollback();
                /** @noinspection PhpVoidFunctionResultUsedInspection */
                die(var_dump($e));
            }
        }
        $mysql->commit();

        return $this->get();
    }

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath() . $this->app->cfg()->fetch('paths', 'blueseal') . '/template/sizegroup_add.php');
        /** @var $em CEntityManager * */
        $sizeEdit = null;
        if (isset($_GET['productSizeGroupId'])) {
            $em = $this->app->entityManagerFactory->create('ProductSizeGroup');
            $sizeEdit = $em->findBySql("SELECT id  
                                             FROM ProductSizeGroup 
                                             WHERE `productSizeMacroGroupId` = (SELECT `productSizeMacroGroupId` 
                                                                                FROM ProductSizeGroup 
                                                                                WHERE id = ?)", array($_GET['productSizeGroupId']));
        }

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'sizeEdit' => $sizeEdit,
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}