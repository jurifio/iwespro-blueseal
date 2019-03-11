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
 * Class CProductCustomFilterPreviewAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 19/04/2018
 * @since 1.0
 */
class CProductCustomFilterPreviewAjaxController extends AAjaxController
{

    public function get(){

        $data = \Monkey::app()->router->request()->getRequestData();

        $cat = $data["cat"];
        $season = $data["season"];
        $shops = $data["shops"];
        $shooting = $data["shooting"];


        /** @var CProductRepo $pRepo */
        $pRepo = \Monkey::app()->repoFactory->create('Product');


        $prod = $pRepo->getProductsByCategoryFullTreeFilters();


    }


}