<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\domain\entities\CProductEan;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductSku;
use bamboo\domain\repositories\CDocumentRepo;

/**
 * Class CProductListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 28/10/2018
 * @since 1.0
 */
class CProductEanListAjaxController extends AAjaxController
{
    public function get()
    {
        $sql = "SELECT pe.id as id, 
                        pe.ean as ean,  
                        pe.productId as productId,
                        pe.productVariantId as productVariantId,
                        pe.productSizeId as productSizeId,
                        concat(pe.productId,'-',pe.productVariantId,'-',productSizeId) as code,
                        pe.usedForParent as usedForParent,
                        if(pe.used=0,'Non Utilizzato','Utilizzato') as used,
                        if(pe.brandAssociate=0,'Non Associato','Associato a Brand') as brandAssociate,
                        pe.dateImport as dateImport,
                        pe.fileImported as fileImported
                        from ProductEan pe
                    ";
        $datatable = new CDataTables($sql, ['id'], $_GET, true);


        $datatable->doAllTheThings(true);


        foreach ($datatable->getResponseSetData() as $key=>$row) {

           $row['used']="<b>".$row['used']."</b>";
            $row['dateImport']=date('w',strtotime($row['dateImport']));
            $row['fileImported']="<b>".$row['fileImported']."</b>";

            }



        return $datatable->responseOut();
    }
}