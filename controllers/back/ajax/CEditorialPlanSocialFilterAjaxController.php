<?php
/**
 * Created by PhpStorm.
 * User: jurif
 * Date: 06/04/2018
 * Time: 16:55
 */

namespace bamboo\controllers\back\ajax;


use bamboo\blueseal\business\CDataTables;
use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CEditorialPlan;
use bamboo\domain\entities\CEditorialPlanDetail;
use bamboo\domain\entities\CEditorialPlanArgument;
use bamboo\domain\entities\CEditorialPlanSocial;

class CEditorialPlanSocialFilterAjaxController extends AAjaxController
{

    /**
     * @return string
     * @throws \Throwable
     */
    public function post()
    {
        $res = [];

        $data = \Monkey::app()->router->request()->getRequestData();
        $ids = $data['ids'];
        $season = $data['season'];
        $photo = ((int)$data['photo'] == 0 ? 'no' : 'si');
        $shooting = ((int)$data['shooting'] == 0 ? 'no' : 'si');
        $shops = $data["shops"];

        /** @var CCategoryManager $cm */
        $cm = \Monkey::app()->categoryManager;

        /** @var CProductCategoryRepo $pcRepo */
        $pcRepo = \Monkey::app()->repoFactory->create('ProductCategory');

        /** @var CProductRepo $pRepo */
        $pRepo = \Monkey::app()->repoFactory->create('Product');

        $allDepth = [];
        foreach ($ids as $id){
            $allDepth[] = $cm->categories()->nestedSet()->nodeDepthById($id);
        }

        $checkDepth = array_unique($allDepth);

        if(count($checkDepth) != 1){
            return "no";
        }


        $prod = [];
        foreach ($ids as $id){
            /** @var CProductCategory $pc */
            $pc = $pcRepo->findOneBy(['id'=>$id]);
            $fath = $pcRepo->fetchParent($id);
            $slug = $pc->getSlug();

            $prod[$fath->slug.'-'.$slug.'-'.$pc->id] = $pRepo->getProductsByCategoryFullTreeFilters($id, $season, $photo, $shooting, $shops);
        }

        foreach ($prod as $key=>$val) {
            $res[$key] = $val->count();
        }



        return json_encode($res);
    }

    public function get(){



        /** @var CObjectCollection $editorialPlanSocial */
        $editorialPlanSocial = \Monkey::app()->repoFactory->create('editorialPlanSocial')->findAll();

        /** @var CEditorialPlanSocial $social */
        foreach ($editorialPlanSocial as $social){
            $res["social"][$social->id] = $social->name;
        }


        return json_encode($res);
    }
}