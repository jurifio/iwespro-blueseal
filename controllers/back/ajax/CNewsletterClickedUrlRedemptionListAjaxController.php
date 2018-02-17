<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CProduct;
use bamboo\domain\repositories\CEmailAddressRepo;
use bamboo\domain\repositories\CEmailRecipientClickedUrlRepo;


/**
 * Class CNewsletterClickedUrlRedemptionListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 16/02/2018
 * @since 1.0
 */
class CNewsletterClickedUrlRedemptionListAjaxController extends AAjaxController
{
    public function get()
    {
        $emailId = \Monkey::app()->router->request()->getRequestData('emailid');
        $emailAddressId = \Monkey::app()->router->request()->getRequestData('emailaddressid');

        $sql = "  SELECT ercl.id as id,
                        ercl.url as url,
                        ercl.type as type,
                        ercl.date as date,
                        ercl.emailId as emailId,
                        ercl.emailAddressId as emailAddressId
                  FROM EmailRecipientClickedUrl ercl
                  WHERE ercl.emailId = $emailId AND ercl.emailAddressId = $emailAddressId";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings('true');

        /** @var CEmailRecipientClickedUrlRepo $urlClickedRepo */
        $urlClickedRepo = \Monkey::app()->repoFactory->create('EmailRecipientClickedUrl');

        /** @var CEmailAddressRepo $emailAddressRepo */
        $emailAddressRepo = \Monkey::app()->repoFactory->create('EmailAddress');

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            $emailAddress = $emailAddressRepo->findOneBy(['id'=>$row["emailAddressId"]])->address;


            $row["url"] = '<a href="' . $row["url"] . '" target="_blank">' . $row["url"] . '</a>';

            /** @var CProduct $product */
            $product = $urlClickedRepo->getProductFromUrl($row["url"]);

            $row["productCode"] = $product->id.'-'.$product->productVariantId;
            $row["productImage"] = '<img src="'.$product->getDummyPictureUrl().'" width="80px">';
            $row["emailAddressId"] = $emailAddress;

            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();
    }
}