<?php
namespace bamboo\controllers\back\ajax;

use bamboo\core\exceptions\BambooException;

/**
 * Class CProductListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>, ${DATE}
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CDetailGetAll extends AAjaxController
{
    public function get()
    {

        $productDetailsCollection = \Monkey::app()->repoFactory->create('ProductDetailTranslation')->findBy(['langId' => 1]);
        $productDetails = [];

        foreach ($productDetailsCollection as $detail) {
            $arr = [];
            $arr['id'] = $detail->productDetailId;
            $arr['item'] = $detail->name;
            $productDetails[] = $arr;
        }
        unset($productDetailsCollection);
        return json_encode($productDetails);
    }
}