<?php


namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CEditorialPlan;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CShooting;

/**
 * Class CSetOnlyCatalogueAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 03/08/2020
 * @since 1.0
 */
class CSetOnlyCatalogueAjaxController extends AAjaxController
{

    public function post()
    {
        $data = $this->app->router->request()->getRequestData();
        $products=$data['products'];
        $onlyCatalogue=$data['onlyCatalogue'];
        $textResult='';
        if($onlyCatalogue==0){
            $textResult='Attivato Per la Vendita';
        }else{
            $textResult='Disattivato per la Vendita</br>';
        }
        $res='';
        $productRepo=\Monkey::app()->repoFactory->create('Product');
        foreach($products as $product){
            $pr=explode('-',$product);
            $findProduct=$productRepo->findOneBy(['id'=>$pr[0],'productVariantId'=>$pr[1]]);
            $findProduct->onlyCatalogue=$onlyCatalogue;
            $findProduct->update();
            $res.=$pr[0].'-'.$pr[1]. ' '. $textResult;
        }
        return $res;

    }
}