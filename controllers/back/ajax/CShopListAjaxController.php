<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CShop;
use bamboo\domain\entities\CAddressBook;

/**
 * Class CShopListAjaxController
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
class CShopListAjaxController extends AAjaxController
{
    public function get()
    {
        $datatable = new CDataTables("Shop",['id'],$_GET,false);
        $datatable->addCondition('id',\Monkey::app()->repoFactory->create('Shop')->getAutorizedShopsIdForUser());

        $shops = \Monkey::app()->repoFactory->create('Shop')->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = \Monkey::app()->repoFactory->create('Shop')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = \Monkey::app()->repoFactory->create('Shop')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];
        $addressBookRepo=\Monkey::app()->repoFactory->create('AddressBook');
        /** @var CShop $shop */
        foreach($shops as $shop){
            if($shop->isVisible==1) {
                $row = [];
                $row['DT_RowId'] = $shop->printId();
                $row['id'] = '<a href="/blueseal/shop?id=' . $shop->printId() . '">' . $shop->printId() . '</a>';
                $row['title'] = $shop->title;
                $row['owner'] = $shop->owner;
                $row['currentSeasonMultiplier'] = $shop->currentSeasonMultiplier;
                $addressbook = $addressBookRepo->findOneBy(['id' => $shop->billingAddressBookId]);
                $row['vatNumber'] = $shop->billingAddressBook ? substr($shop->billingAddressBook->vatNumber, 6, 13) : null;
                $row['pastSeasonMultiplier'] = $shop->pastSeasonMultiplier;
                $row['referrerEmails'] = implode('<br />', explode(';', $shop->referrerEmails));
                $row['billingEmails'] = implode('<br />', explode(';', $shop->billingEmails));
                $row['billingContact']=$shop->billingContact;
                $row['saleMultiplier'] = $shop->saleMultiplier;
                $row['minReleasedProducts'] = $shop->minReleasedProducts;
                $row['releasedProducts'] = $shop->getActiveProductCount();
                $row['isActive'] = $shop->isActive;

                $users = [];
                foreach ($shop->user as $user) {
                    $users[] = $user->email;
                }
               if ($shop->hasEcommerce){
                   $shopHasCounter=\Monkey::app()->repoFactory->create('ShopHasCounter')->findOneBy(['shopId'=>$shop->id]);
                   $numberReceipt=$shop->receipt.'-'.$shopHasCounter->receiptCounter;
                   $row['numberReceipt']=$numberReceipt;
                   $numberInvoiceUe =$shop->invoiceUe.'-'.$shopHasCounter->invoiceCounter;
                   $row['numberInvoiceUe']=$numberInvoiceUe;
                   $numberInvoiceExtraUe =$shop->invoiceExtraUe.'-'.$shopHasCounter->invoiceExtraUeCounter;
                   $row['numberInvoiceExtraUe']=$numberInvoiceExtraUe;
               }else{
                   $row['numberReceipt']='';
                   $row['numberInvoiceUe']='';
                   $row['numberInvoiceExtraUe']='';
               }
                $row['users'] = implode('<br />', $users);
                $row['iban'] = $shop->billingAddressBook ? $shop->billingAddressBook->iban : null;

                $response['data'][] = $row;
            }
        }
        return json_encode($response);
    }
}