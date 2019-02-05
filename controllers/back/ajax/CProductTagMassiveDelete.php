<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\domain\entities\CTag;
use bamboo\domain\repositories\CProductRepo;
use bamboo\domain\repositories\CTagRepo;
use bamboo\ecommerce\views\widget\VBase;

/**
 * Class CProducTagMassive
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 29/04/2016
 * @since 1.0
 */
class CProductTagMassiveDelete extends AAjaxController
{

    /**
     * @return bool
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function get()
    {
        $season = \Monkey::app()->router->request()->getRequestData('seasonVar');
        $brand = \Monkey::app()->router->request()->getRequestData('brandVar');
        $color = \Monkey::app()->router->request()->getRequestData('colorVar');

        if (empty($season) && empty($brand) && empty($color)) return false;

        $cond = '';

        if (!empty($season)) $cond .= ' AND p.productSeasonId = ' . $season;
        if (!empty($brand)) $cond .= ' AND p.productBrandId = ' . $brand;
        if (!empty($color)) $cond .= ' AND p.productColorGroupId = ' . $color;

        $tagIds = \Monkey::app()->dbAdapter->query('SELECT pht.tagId
                                                              FROM Product p
                                                              JOIN ProductHasTag pht ON p.id = pht.productId AND p.productVariantId = pht.productVariantId
                                                              WHERE 1=1 ' . $cond .
            ' GROUP BY pht.tagId', [])->fetchAll();

        /** @var CTagRepo $tagRepo */
        $tagRepo = \Monkey::app()->repoFactory->create('Tag');

        $tagNames = [];
        foreach ($tagIds as $tagId) {

            $tagNames[$tagId['tagId']] = $tagRepo->findOneBy(['id' => $tagId['tagId']])->getLocalizedName();
        }

        return json_encode($tagNames);
    }


}