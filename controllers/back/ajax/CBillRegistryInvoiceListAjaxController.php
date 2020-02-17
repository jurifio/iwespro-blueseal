<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CBillRegistryProduct;
use bamboo\domain\entities\CBillRegistryGroupProduct;
use bamboo\domain\entities\CBillRegistryCategoryProduct;

/**
 * Class CBillRegistryInvoiceListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 10/02/2020
 * @since 1.0
 */
class CBillRegistryInvoiceListAjaxController extends AAjaxController
{
    public function get()
    {
        $sql = "SELECT
                      `bri`.`id`                                            AS `id`,
                       concat(`bri`.`invoiceNumber`,'-',`bri`.`invoiceYear`)                                       as `invoice`,
                      `brc`.`companyName`                                      AS `companyName`,
                      `bri`.`netTotal`                                         AS `netTotal`,
                      `bri`.`vat`             AS `vat`,
                      `bri`.`grossTotal`             AS `grossTotal`,
                      `bri`.`invoiceDate`             AS `invoiceDate`,
                      `brtp`.`name` as typePayment,  
                      if(`bri`.`isPaid`=1,'Si','No')             AS `isPaid`,
                      if(`bri`.`isSent`=1,'Si','No')             AS `isSent`
                    FROM `BillRegistryInvoice` `bri`
                      JOIN `BillRegistryClient` `brc` on `bri`.`billRegistryClientId`=`brc`.`id`
                        join `BillRegistryClientBillingInfo` `brcbi` on `bri`.`billRegistryClientId`
                      JOIN `BillRegistryTypePayment` `brtp` on `brcbi`.`billRegistryTypePaymentId`= `brtp`.`id`";
        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings();

        $invoiceEdit = $this->app->baseUrl(false) . "/blueseal/anagrafica/fatture-modifica?id=";
        /** @var CBillRegistryInvoice $billRegistryInvoiceRepo */
        $billRegistryInvoiceRepo = \Monkey::app()->repoFactory->create('BillRegistryInvoice');
        foreach ($datatable->getResponseSetData() as $key => $row) {
            /** @var CBillRegistryInvoice $billRegistryInvoice */
            $billRegistryInvoice = $billRegistryInvoiceRepo->findOne([$row['id']]);
            $row = [];
            $row["DT_RowId"] = 'row__' . $billRegistryProduct->printId();
            $row['id'] = '<a href="'.$invoiceEdit.$billRegistryInvoice->id.'">'.$billRegistryInvoice->id.'</a>';
            $date=new \DateTime($billRegistryInvoice->invoiceDate);
            $row['invoiceDate']=$date->format('d-m-Y');
            $datatable->setResponseDataSetRow($key, $row);
        }

        return $datatable->responseOut();
    }
}