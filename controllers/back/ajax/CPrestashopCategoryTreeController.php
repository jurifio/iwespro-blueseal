<?php

namespace bamboo\controllers\back\ajax;
use bamboo\blueseal\marketplace\prestashop\CPrestashopCategory;
use bamboo\domain\entities\CProductCategory;

/**
 * Class CPrestashopCategoryTreeController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 11/02/2019
 * @since 1.0
 */
class CPrestashopCategoryTreeController extends AAjaxController
{
    /**
     * @return bool
     * @throws \Exception
     */
    public function post(){

        $catId = \Monkey::app()->router->request()->getRequestData('id');

        /** @var CProductCategory $prodCat */
        $prodCat = \Monkey::app()->repoFactory->create('ProductCategory')->findOneBy(['id'=>$catId]);

        $prestashopCategory = new CPrestashopCategory();

        if($prestashopCategory->addNewCategories($prodCat)) return true;

        return false;
    }

    /**
     * @return string
     * @throws \PrestaShopWebserviceException
     * @throws \bamboo\core\exceptions\BambooException
     */
    public function delete(){

        $catId = \Monkey::app()->router->request()->getRequestData('id');

        /** @var CProductCategory $prodCat */
        $prodCat = \Monkey::app()->repoFactory->create('ProductCategory')->findOneBy(['id'=>$catId]);

        $prestashopCategory = new CPrestashopCategory();

        $res = $prestashopCategory->deletePrestahopCategory($prodCat);

        if(empty($res['notDeleted'])){
            return 'Categoria eliminata con successo';
        } else {
            return 'Categoria eliminata per gli shop: ' . implode(', ', $res['deleted']) . ' Categoria non eliminata per gli shop: ' . implode(', ', $res['notDeleted']);
        }
    }
}