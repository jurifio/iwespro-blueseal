<?php

namespace bamboo\controllers\back\ajax;

/**
 * Class CRbacRolesTreeController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 20/03/2018
 * @since 1.0
 */
class CRbacRolesTreeController extends AAjaxController
{
    public function get()
    {
        // $cache = \Monkey::app()->cacheService->getCache("misc")->get("FullCategoryTreeAsJSON");
        $cache = false;
        if (!$cache) {
            //$cache = $this->app->categoryManager->categories()->treeToJson(1);
            $roles = $this->app->rbacManager->roles()->treeToJson(1,'title');
            //$this->app->cacheService->getCache("misc")->set("FullCategoryTreeAsJSON", $cache, 13000);
        }
        return $roles;
    }

    public function post()
    {
        $data = \Monkey::app()->router->request()->getRequestData();
        $id = \Monkey::app()->rbacManager->addRole($data['title'],$data['description'],$data['parent']);
        //$id = \Monkey::app()->categoryManager->categories()->add($data['title'],$data['description'],$data['parent']);

        //\Monkey::app()->cacheService->getCache("misc")->delete("FullCategoryTreeAsJSON");
        return $id;
    }

    public function put()
    {
        /*
        $data = \Monkey::app()->router->request()->getRequestData();
        $node = $data['node'];
        $newParent = $data['newParent'];

        \Monkey::app()->categoryManager->categories()->nestedSet()->moveSubreeToNodeTreeSteps($node,$newParent);
        \Monkey::app()->cacheService->getCache("misc")->delete("FullCategoryTreeAsJSON");

        return true;
        */
    }

    public function delete()
    {
        /*
        $data = \Monkey::app()->router->request()->getRequestData();
        $node = $data['node'];

        $res = \Monkey::app()->repoFactory->create('RbacRole')->deleteCategoryAndDescendant($node);

        if(!$res) {
            $res = [];
            \Monkey::app()->router->response()->raiseProcessingError();
            $products = \Monkey::app()->repoFactory->create('Product')->getProductsByCategoryFullTree($node);
            foreach ($products as $product) {
                $res[] = $product->printId();
            }
            return json_encode($res);
        }
        return true;
    */
    }

}