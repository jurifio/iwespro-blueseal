<?php
namespace bamboo\controllers\back\ajax;

use bamboo\core\intl\CLang;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\widget\VBase;

/**
 * Class CProductMarketingAnalyze
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CProductMarketingAnalyze extends AAjaxController
{

    public function get()
    {
        $ids = $this->app->router->request()->getRequestData('row');
        $product = $this->app->repoFactory->create('Product')->findOneByStringId($ids);
        $queryStronza = "SELECT
                              count(id) as conto,
                              max(creationDate) as firstSeen,
                              min(creationDate) as lastSeen,
                              ifnull(campaignData, 'nessuna') as campaignData
                            FROM ActivityLog
                            WHERE routeName = 'Pagina Prodotto'
                              AND actionArgs LIKE ? GROUP BY campaignData";
        $res = $this->app->dbAdapter->query($queryStronza,['%'.$product->id.'%'.$product->productVariantId.'%'])->fetchAll();
        return json_encode($res);
    }
}