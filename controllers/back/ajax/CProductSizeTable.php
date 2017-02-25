<?php
/**
 * Created by PhpStorm.
 * User: Fabrizio Marconi
 * Date: 20/05/2015
 * Time: 13:00
 */
namespace bamboo\controllers\back\ajax;
use bamboo\domain\entities\CProduct;


/**
 * Class CProductSizeTable
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CProductSizeTable extends AAjaxController
{
    public function get(){
        $productId = trim(\Monkey::app()->router->request()->getRequestData('productId'));
        /** @var CProduct $product */
        $product = $this->app->repoFactory->create('Product')->findOneByStringId($productId);
        $shopIds = $this->app->repoFactory->create('Shop')->getAutorizedShopsIdForUser();
        return json_encode($product->getStockSituationTable($shopIds));
    }
}