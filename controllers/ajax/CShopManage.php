<?php

namespace bamboo\blueseal\controllers\ajax;

use bamboo\domain\entities\CAddressBook;


/**
 * Class CUserList
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 22/07/2016
 * @since 1.0
 */
class CShopManage extends AAjaxController
{
	public function get()
	{
	    $shopId = $this->app->router->request()->getRequestData('id');
	    $shops = $this->app->repoFactory->create('Shop')->getAutorizedShopsIdForUser();
	    if(in_array($shopId,$shops)) {
	        $shop = $this->app->repoFactory->create('Shop')->findOneByStringId($shopId);
	        $shop->user;
	        $shop->billingAddressBook;
	        $shop->shippingAddressBook;
            return json_encode($shop);
        } else {
            $this->app->router->response()->raiseUnauthorized();
            return 'Bad Request';
        }
	}

	public function put()
    {
        $shopData = $this->app->router->request()->getRequestData('shop');
        $shopsId = $this->app->repoFactory->create('Shop')->getAutorizedShopsIdForUser();
        if(in_array($shopData['id'],$shopsId)) {
            $shop = $this->app->repoFactory->create('Shop')->findOneByStringId($shopData['id']);
            $shop->title = $shopData['title'];
            $shop->owner = $shopData['owner'];
            $shop->referrerEmails = $shopData['referrerEmails'];
            $shop->iban = $shopData['iban'];
            if($this->app->getUser()->hasPermission('allShops')) {
                $shop->currentSeasonMultiplier = $shopData['currentSeasonMultiplier'];
                $shop->pastSeasonMultiplier = $shopData['pastSeasonMultiplier'];
                $shop->saleMultiplier = $shopData['saleMultiplier'];
                $config = $shop->config;
                $config['refusalRate'] = $shopData['config']['refusalRate'] ?? null;
                $config['refusalRateLastMonth'] = $shopData['config']['refusalRateLastMonth'] ?? null;
                $config['reactionRate'] = $shopData['config']['reactionRate'] ?? null;
                $config['reactionRateLastMonth'] = $shopData['config']['reactionRateLastMonth'] ?? null;
                $shop->config = $config;
            }

            $billingAddressBookData = $shopData['billingAddressBook'];
            $billingAddressBook = $this->getAndFillAddressData($billingAddressBookData);
            if(isset($billingAddressBook->id)) $billingAddressBook->update();
            else $billingAddressBook->id = $billingAddressBook->insert();

            $shop->billingAddressBookId = $billingAddressBook->id;

            foreach ($shopData['shippingAddresses'] as $shippingAddressData) {
                $shippingAddress = $this->getAndFillAddressData($shippingAddressData);
                if(is_null($shippingAddress)) continue;
                if(isset($shippingAddress->id)) $shippingAddress->update();
                else $shippingAddress->id = $shippingAddress->insert();
                $this->app->dbAdapter->insert('ShopHasShippingAddressBook',['shopId'=>$shop->id,'addressBookId'=>$shippingAddress->id],false,true);
            }

            return $shop->update();
        } else {
            $this->app->router->response()->raiseUnauthorized();
            return 'Bad Request';
        }
    }

    /**
     * @param $addressBookData
     * @return \bamboo\core\db\pandaorm\entities\AEntity|CAddressBook|null
     */
    private function getAndFillAddressData($addressBookData) {
	    if(isset($addressBookData['id'])) $addressBook = $this->app->repoFactory->create('AddressBook')->findOneByStringId($addressBookData['id']);
	    else $addressBook = $this->app->repoFactory->create('AddressBook')->getEmptyEntity();
	    try {
            /** @var CAddressBook $addressBook */
            $addressBook->name = $addressBookData['name'] ?? null;
            $addressBook->subject = $addressBookData['subject'];
            $addressBook->address = $addressBookData['address'];
            $addressBook->extra = $addressBookData['extra'] ?? null;
            $addressBook->city = $addressBookData['city'];
            $addressBook->countryId = $addressBookData['countryId'];
            $addressBook->postcode = $addressBookData['postcode'];
            $addressBook->phone = $addressBookData['phone'] ?? null;
            $addressBook->cellphone = $addressBookData['cellphone'] ?? null;
            $addressBook->province = $addressBookData['province'] ?? null;

            if(!isset($addressBook->id)) {
                $addressBookC = $this->app->repoFactory->create('AddressBook')->findOneBy(['checksum'=>$addressBook->calculateChecksum()]);
                if(!is_null($addressBookC)) $addressBook->id = $addressBookC->id;
            }
        } catch (\Throwable $e) {
	        return null;
        }

        return $addressBook;
    }
}