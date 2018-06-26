<?php

namespace bamboo\controllers\back\ajax;
use bamboo\core\utils\slugify\CSlugify;
use bamboo\domain\entities\CProductCategory;
use bamboo\domain\entities\CProductCategoryTranslation;

/**
 * Class CCategoryTreeOperationController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 25/06/2018
 * @since 1.0
 */
class CCategoryTreeOperationController extends AAjaxController
{

    /**
     * @return string
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function put(){

        $id = \Monkey::app()->router->request()->getRequestData('node');
        $name = \Monkey::app()->router->request()->getRequestData('newName');


        /** @var CProductCategory $pc */
        $pc = \Monkey::app()->repoFactory->create('ProductCategory')->findOneBy(['id'=>$id]);

        /** @var CProductCategoryTranslation $prodCatT */
        $prodCatT = \Monkey::app()->repoFactory->create('ProductCategoryTranslation')->findOneBy(['productCategoryId'=>$id, 'langId'=>1]);

        /** @var CSlugify $slugy */
        $slugy = new CSlugify();

        $pc->slug = $slugy->slugify(trim($name));
        $pc->update();

        $prodCatT->slug = $slugy->slugify(trim($name));
        $prodCatT->name = $name;
        $prodCatT->update();

        return 'Nomi della Categoria aggiornata con successo';

    }
}