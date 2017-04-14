<?php
namespace bamboo\controllers\back\ajax;

/**
 * Class CProductReadRawDetails
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
class CProductReadRawDetails extends AAjaxController
{

    public function get()
    {
        $details = [];
        $productIds = $this->app->router->request()->getRequestData('productIds');
        foreach ($this->app->repoFactory->create('Product')->findOneByStringId($productIds)->shopHasProduct as $shopHasProduct){
            if(is_null($shopHasProduct->dirtyProduct)) continue;
            foreach ($shopHasProduct->dirtyProduct->dirtyDetail as $dirtyDetail) {
                $detail['label'] = $dirtyDetail->label;
                $detail['content'] = $dirtyDetail->content;
                $details[] = $detail;
            }
        }
        return json_encode($details);
    }
}