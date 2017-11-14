<?php

namespace bamboo\controllers\back\ajax;

use bamboo\domain\entities\CAddressBook;
use bamboo\domain\entities\CShop;


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
        if (in_array($shopId, $shops)) {
            /** @var CShop $shop */
            $shop = $this->app->repoFactory->create('Shop')->findOneByStringId($shopId);
            $shop->user;
            $shop->billingAddressBook;
            $shop->shippingAddressBook;
            $shop->productStatistics = $shop->getDailyActiveProductStatistics();
            $shop->orderStatistics = $shop->getDailyOrderFriendStatistics();
            return json_encode($shop);
        } else {
            $this->app->router->response()->raiseUnauthorized();
            return 'Bad Request';
        }
    }

    public function put()
    {
        try {
            $shopData = $this->app->router->request()->getRequestData('shop');
            $shopsId = $this->app->repoFactory->create('Shop')->getAutorizedShopsIdForUser();
            if (in_array($shopData['id'], $shopsId)) {
                $shop = $this->app->repoFactory->create('Shop')->findOneByStringId($shopData['id']);
                $shop->title = $shopData['title'];
                $shop->owner = $shopData['owner'];
                $shop->referrerEmails = $shopData['referrerEmails'];
                if ($this->app->getUser()->hasPermission('allShops')) {
                    $shop->currentSeasonMultiplier = $shopData['currentSeasonMultiplier'];
                    $shop->pastSeasonMultiplier = $shopData['pastSeasonMultiplier'];
                    $shop->saleMultiplier = $shopData['saleMultiplier'];
                    $shop->minReleasedProducts = $shopData['minReleasedProducts'];
                    $config = $shop->config;
                    $config['refusalRate'] = $shopData['config']['refusalRate'] ?? null;
                    $config['refusalRateLastMonth'] = $shopData['config']['refusalRateLastMonth'] ?? null;
                    $config['reactionRate'] = $shopData['config']['reactionRate'] ?? null;
                    $config['reactionRateLastMonth'] = $shopData['config']['reactionRateLastMonth'] ?? null;
                    $config['accountStatus'] = $shopData['config']['accountStatus'] ?? null;
                    $config['accountType'] = $shopData['config']['accountType'] ?? null;
                    $config['photoCost'] = $shopData['config']['photoCost'] ?? null;
                    $config['shootingTransportCost'] = $shopData['config']['shootingTransportCost'] ?? null;
                    $config['orderTransportCost'] = $shopData['config']['orderTransportCost'] ?? null;
                    $shop->config = $config;
                }

                $billingAddressBookData = $shopData['billingAddressBook'];
                $billingAddressBook = $this->getAndFillAddressData($billingAddressBookData);

                if (!is_null($billingAddressBook)) {
                    if (isset($billingAddressBook->id)) $billingAddressBook->update();
                    else $billingAddressBook->id = $billingAddressBook->insert();
                    $shop->billingAddressBookId = $billingAddressBook->id;
                }


                foreach ($shopData['shippingAddresses'] as $shippingAddressData) {
                    $shippingAddress = $this->getAndFillAddressData($shippingAddressData);
                    if (is_null($shippingAddress)) continue;
                    if (isset($shippingAddress->id)) $shippingAddress->update();
                    else $shippingAddress->id = $shippingAddress->insert();
                    $this->app->dbAdapter->insert('ShopHasShippingAddressBook', ['shopId' => $shop->id, 'addressBookId' => $shippingAddress->id], false, true);
                }

                $x = $shop->update();
                $this->app->dbAdapter->commit();
                return $x;
            } else {
                $this->app->router->response()->raiseUnauthorized();
                return 'Bad Request';
            }
        } catch (\Throwable $e) {
            $this->app->dbAdapter->rollBack();
            throw $e;
        }
    }

    /**
     * @param $addressBookData
     * @return \bamboo\core\db\pandaorm\entities\AEntity|CAddressBook|null
     */
    private function getAndFillAddressData($addressBookData)
    {
        $ok = false;
        foreach ($addressBookData as $field) {
            if (!empty($field)) {
                $ok = true;
                break;
            }
        }
        if (!$ok) return null;
        $addressBook = $this->app->repoFactory->create('AddressBook')->findOneByStringId($addressBookData['id']);
        if (is_null($addressBook)) $addressBook = $this->app->repoFactory->create('AddressBook')->getEmptyEntity();
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
            $addressBook->iban = $addressBookData['iban'] ?? null;
            $addressBook->note = $addressBookData['note'] ?? null;

            if (!isset($addressBook->id)) {
                $addressBookC = $this->app->repoFactory->create('AddressBook')->findOneBy(['checksum' => $addressBook->calculateChecksum()]);
                if (!is_null($addressBookC)) $addressBook->id = $addressBookC->id;
            }
        } catch (\Throwable $e) {
            return null;
        }

        return $addressBook;
    }
}