<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\db\pandaorm\adapter\CMySQLAdapter;
use bamboo\core\utils\slugify\CSlugify;

/**
 * Class CColorManageController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>, ${DATE}
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CColorManageController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";

    public function post()
    {
        throw new \Exception('DEBUG ME; TO CHANGE AFTER COLOR REFACTOR');
        $blueseal = $this->app->baseUrl(false) . '/blueseal';
        $slugify = new CSlugify();
        $datas = $this->app->router->request()->getRequestData();
        /** @var CMySQLAdapter $mysql */
        foreach ($datas as $key => $val) {
            $keys = explode('_', $key);
            if ($keys[1] == 'slug') continue;
            if ($keys[1] == 'id') continue;
            $langId = $keys[2];
            $name = $val;
            $slug = empty($datas['ProductColorGroup_slug_' . $langId]) ? $slugify->slugify($name) : $slugify->slugify($datas['ProductColorGroup_slug_' . $langId]);
            try {
                $colorGroup = \Monkey::app()->repoFactory->create('ProductColorGroup')->findOneBy(['id'=>$datas['ProductColorGroup_id'], 'langId'=>$langId]);
                if (!is_null($colorGroup)) {
                    $colorGroup->langId = $langId;
                    $colorGroup->name = $name;
                    $colorGroup->slug = $slug;
                    $colorGroup->update();
                } else {
                    $colorGroup = \Monkey::app()->repoFactory->create("ProductColorGroup")->getEmptyEntity();

                    $colorGroup->id = $mysql->query("SELECT MAX(id) AS id FROM ProductColorGroup", array())->fetch();
                    $colorGroup->id ++;
                    $colorGroup->langId = $langId;
                    $colorGroup->name = $name;
                    $colorGroup->slug = $slug;
                    $colorGroup->insert();
                }
            } catch (\Throwable $e) {
                $this->app->router->response()->raiseUnauthorized();
            }
           /*if (!is_null($datas['ProductColorGrup_id'])) {
                $id = $datas['ProductColorGrup_id'];
                $mysql->update("ProductColorGroup", array("name" => $name, "slug" => $slug), array("id" => $id, "langId" => $langId));
            } else {
                if (empty($presentId)) {
                    $presentId = $mysql->query("SELECT MAX(id) AS id FROM ProductColorGroup", array())->fetch();
                    $presentId = $presentId['id'] + 1;
                }
                $mysql->insert("ProductColorGroup", array("id" => $presentId, "langId" => $langId, "slug" => $slug, "name" => $name));
            }*/
        }
        return "ok";
    }

    public function put()
    {
        $blueseal = $this->app->baseUrl(false) . '/blueseal';
        $slugify = new CSlugify();
        $datas = $this->app->router->request()->getRequestData();
        /** @var CMySQLAdapter $mysql */
        foreach ($datas as $key => $val) {
            $keys = explode('_', $key);
            if ($keys[1] == 'slug') continue;
            if ($keys[1] == 'id') continue;
            $langId = $keys[2];
            $name = $val;
            $slug = empty($datas['ProductColorGroup_slug_' . $langId]) ? $slugify->slugify($name) : $slugify->slugify($datas['ProductColorGroup_slug_' . $langId]);
            if(empty($slug) && empty($name)) continue; // delete
            try {
                $colorGroup = \Monkey::app()->repoFactory->create('ProductColorGroupTranslation')->findOneBy(['productColorGroupId'=>$datas['ProductColorGroup_id'], 'langId'=>$langId]);
                if (!is_null($colorGroup)) {
                    $colorGroup->langId = $langId;
                    $colorGroup->name = $name;
                    $colorGroup->slug = $slug;
                    $colorGroup->update();
                } else {
                    $colorGroup = \Monkey::app()->repoFactory->create('ProductColorGroupTranslation')->getEmptyEntity();
                    $colorGroup->productColorGroupId = $datas['ProductColorGroup_id'];
                    $colorGroup->langId = $langId;
                    $colorGroup->name = $name;
                    $colorGroup->slug = $slug;
                    $colorGroup->insert();
                }
            } catch (\Throwable $e) {
                throw $e;
            }
        }
    }

}