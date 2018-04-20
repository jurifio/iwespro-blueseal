<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\base\CObjectCollection;
use bamboo\core\theming\nestedCategory\CCategoryManager;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductCategory;
use bamboo\domain\entities\CProductHasShooting;
use bamboo\domain\entities\CProductSeason;
use bamboo\domain\entities\CShooting;
use bamboo\domain\entities\CShop;
use bamboo\domain\repositories\CProductCategoryRepo;
use bamboo\domain\repositories\CProductHasShootingRepo;
use bamboo\domain\repositories\CProductRepo;
use bamboo\domain\repositories\CShootingRepo;
use bamboo\domain\repositories\CShopRepo;


/**
 * Class CProductCustomFilterAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 17/04/2018
 * @since 1.0
 */
class CProductCustomFilterAjaxController extends AAjaxController
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

        /** @var CShopRepo $shopRepo */
        $shopRepo = \Monkey::app()->repoFactory->create('Shop');

        /** @var CObjectCollection $shops */
        $shops =  $shopRepo->getAutorizedShopsForUser();

        $res = [];

        /** @var CShop $shop */
        foreach ($shops as $shop) {
            $res["shop"][$shop->id] = $shop->name;
        }

        /** @var CObjectCollection $seasons */
        $seasons = \Monkey::app()->repoFactory->create('ProductSeason')->findAll();

        /** @var CProductSeason $season */
        foreach ($seasons as $season){
            $res["season"][$season->id] = $season->name;
        }


        return json_encode($res);
    }


}