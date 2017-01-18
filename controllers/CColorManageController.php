<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\db\pandaorm\adapter\CMySQLAdapter;
use bamboo\core\utils\slugify\CSlugify;

/**
 * Class CColorManageController
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
                $colorGroup = $this->app->repoFactory->create('ProductColorGroup')->findOneBy(['id'=>$datas['ProductColorGroup_id'], 'langId'=>$langId]);
                if (!is_null($colorGroup)) {
                    $colorGroup->langId = $langId;
                    $colorGroup->name = $name;
                    $colorGroup->slug = $slug;
                    $colorGroup->update();
                } else {
                    $colorGroup = $this->app->repoFactory->create("ProductColorGroup")->getEmptyEntity();

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
                $colorGroup = $this->app->repoFactory->create('ProductColorGroup')->findOneBy(['id'=>$datas['ProductColorGroup_id'], 'langId'=>$langId]);
                if (!is_null($colorGroup)) {
                    $colorGroup->langId = $langId;
                    $colorGroup->name = $name;
                    $colorGroup->slug = $slug;
                    $colorGroup->update();
                } else {
                    $colorGroup->id = $datas['ProductColorGroup_id'];
                    $colorGroup->langId = $langId;
                    $colorGroup->name = $name;
                    $colorGroup->slug = $slug;
                    $colorGroup->insert();
                }
            } catch (\Throwable $e) {
                $this->app->router->response()->raiseUnauthorized();
            }
           /* if (isset($colorGroup)) {
                $id = $datas['ProductColorGroup_id'];
                $mysql->update("ProductColorGroup", array("name" => $name, "slug" => $slug), array("id" => $id, "langId" => $langId));
            } else {
               $mysql->insert("ProductColorGroup", array("id" => $datas['ProductColorGroup_id'], "langId" => $langId, "slug" => $slug, "name" => $name));
            }*/
        }
    }

}