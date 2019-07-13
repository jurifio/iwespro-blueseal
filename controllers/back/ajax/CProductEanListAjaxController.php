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
        //inserita modifica per conteggio ean disponibili
        $sql = "SELECT pe.id as id, 
                     (select count(ean)  as countFreeEan from ProductEan pc where pc.productId IS   NULL ) AS countFreeEan,
                        pe.ean as ean,  
                        pe.productId as productId,
                        pe.productVariantId as productVariantId,
                        pe.productSizeId as productSizeId,
                        concat(pe.productId,'-',pe.productVariantId,'-',productSizeId) as code,
                        pe.usedForParent as usedForParent,
                        if(pe.used=0,'Non Utilizzato','Utilizzato') as used,
                        if(pe.brandAssociate=0,'Non Associato',pe.brandAssociate) as brandAssociate,
                        if(pe.shopId=null,'Non Associato',pe.shopId) as shopId,
                        pe.dateImport as dateImport,
                        pe.fileImported as fileImported,
                        p.qty,
                        ps.name as productStatus
                        from ProductEan pe
                       left outer JOIN Product p ON p.id = pe.productId AND p.productVariantId = pe.productVariantId
                       left outer  JOIN ProductStatus ps ON p.productStatusId = ps.id
                    ";
        $datatable = new CDataTables($sql, ['id'], $_GET, true);



        $datatable->doAllTheThings(true);
        /** @var CRepo $nshopRepo  */
        $nshopRepo=\Monkey::app()->repoFactory->create('Shop');
        /** @var CRepo $nbrandRepo  */
        $nbrandRepo=\Monkey::app()->repoFactory->create('ProductBrand');


        foreach ($datatable->getResponseSetData() as $key=>$row) {

            $row['used']="<b>".$row['used']."</b>";
            $row['dateImport']=date('w',strtotime($row['dateImport']));
            $row['fileImported']="<b>".$row['fileImported']."</b>";
            /** @var CShop $shop */
            $shop = $nshopRepo->findOneBy(['id'=>$row['shopId']]);
            if(null==$shop){
                $row['shopId']="non Assegnato";
            }else {
                $row['shopId'] = $shop->title;
            }
            /** @var CProductBrand $brand */
            $brand = $nbrandRepo->findOneBy(['id'=>$row['brandAssociate']]);
            if(null==$brand){
                $row['brandAssociate']="non Assegnato";
            } else {
                $row['brandAssociate'] = $brand->name;
            }
            $datatable->setResponseDataSetRow($key,$row);

            }



        return $datatable->responseOut();
    }
}