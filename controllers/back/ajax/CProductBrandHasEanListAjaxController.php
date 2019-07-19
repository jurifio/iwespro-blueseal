<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CProductEan;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductSku;
use bamboo\domain\entities\CShop;
use bamboo\domain\entities\CProductBrand;
use bamboo\domain\repositories\CDocumentRepo;

/**
 * Class CProductBrandHasEanListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 18/07/2019
 * @since 1.0
 */
class CProductBrandHasEanListAjaxController extends AAjaxController
{
    public function get()
    {
        //inserita modifica per conteggio ean disponibili
        $sql = "SELECT pb.id as id, 
                       pb.name as name,
                        pb.hasAggregator as hasAggregatorEan,
                       pb.hasMarketplaceRights as hasMarketplaceRights,
                       pb.hasExternalEan as hasExternalEan
                        from ProductBrand pb 
                    ";
        $datatable = new CDataTables($sql, ['id'], $_GET, true);



        $datatable->doAllTheThings(true);
        /** @var CRepo $nshopRepo  */
        $nshopRepo=\Monkey::app()->repoFactory->create('Shop');
        /** @var CRepo $nbrandRepo  */
        $nbrandRepo=\Monkey::app()->repoFactory->create('ProductBrand');


        foreach ($datatable->getResponseSetData() as $key=>$row) {

            $row['name']="<b>".$row['name']."</b>";
            ($row['hasMarketplaceRights']==0)? $row['hasMarketplaceRights']='No' : $row['hasMarketplaceRights']='Si';
            ($row['hasExternalEan']==0)? $row['hasExternalEan']='No' : $row['hasExternalEan']='Si';
            ($row['hasAggregatorEan']==0)? $row['hasAggregatorEan']='No' : $row['hasAggregatorEan']='Si';

            $datatable->setResponseDataSetRow($key,$row);

            }



        return $datatable->responseOut();
    }
}