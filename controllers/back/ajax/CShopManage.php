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
        if (in_array($shopId,$shops)) {
            $cres = \Monkey::app()->dbAdapter->query('select id as id from CouponType where `name` like \'%PostSelling%\' and remoteShopId=' . $shopId,[])->fetchAll();
            if (count($cres) > 0) {
                foreach ($cres as $cresult) {
                    $couponType = $cresult['id'];
                }
            } else {
                $couponType = '0';
            }


            /** @var CShop $shop */
            $shop = $this->app->repoFactory->create('Shop')->findOneByStringId($shopId);
            $shop->user;
            $shop->billingAddressBook;
            $shop->couponType = $couponType;

            // $shop->shippingAddressBook;
            //$shop->shippingAddressBook;
            $shippingAddressBook = [];
            $shopHasShippingAddressBook = \Monkey::app()->repoFactory->create('ShopHasShippingAddressBook')->findBy(['shopId' => $shopId]);
            $i = 0;
            foreach ($shopHasShippingAddressBook as $shipping) {

                $addressBookId = $shipping->addressBookId;
                $shippingAddressBooks = \Monkey::app()->repoFactory->create('AddressBook')->findOneBy(['id' => $addressBookId]);
                if ($i < 3) {
                    $shippingAddressBook[] = ['id' => $shippingAddressBooks->id,
                        'name' => $shippingAddressBooks->name,
                        'subject' => $shippingAddressBooks->subject,
                        'address' => $shippingAddressBooks->address,
                        'extra' => $shippingAddressBooks->extra,
                        'city' => $shippingAddressBooks->city,
                        'postcode' => $shippingAddressBooks->postcode,
                        'phone' => $shippingAddressBooks->phone,
                        'cellphone' => $shippingAddressBooks->cellphone,
                        'province' => $shippingAddressBooks->province,
                        'countryId' => $shippingAddressBooks->countryId
                    ];
                } else {
                    break;
                }
                $i++;
            }
            $shop->shippingAddressBooks = $shippingAddressBook;
            $aggregatorHasShopFind = \Monkey::app()->repoFactory->create('AggregatorHasShop')->findBy(['shopId' => $shopId]);


            $aggregatorHasShop=[];
            $sql='select ahs.id as id,`ma`.`name` as `name`, ahs.imgAggregator as imgAggregator,`m`.`type` as typeAggregator, ahs.marketplaceId as marketplaceId,ma.id as marketplaceAccountId, ma.config as config, ma.isActive as isActive  from MarketplaceAccount ma join
            Marketplace m on ma.marketplaceId=m.id join AggregatorHasShop ahs on m.id=ahs.marketplaceId where m.type !="marketplace" and ahs.shopId='.$shopId;
            $res = \Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
            foreach ($res as $result) {
                $aggregatorHasShop[]=['id'=>$result['id'],
                                      'name'=>$result['name'],
                                      'imgAggregator'=>$result['imgAggregator'],
                                      'marketplaceAccountId'=>$result['marketplaceAccountId'],
                                      'marketplaceId'=>$result['marketplaceId'],
                                      'isActive'=>$result['isActive'],
                                      'typeAggregator'=>$result['typeAggregator']];
            }
            $shop->aggregatorHasShop = $aggregatorHasShop;
            $marketplaceHasShop=[];
            $sql='select ahs.id as id,`ma`.`name` as `name`, `m`.`type` as typeMarketplace, m.id as marketplaceId, ma.id as marketplaceAccountId, ma.config as config, ma.isActive as isActive  from MarketplaceAccount ma join
            Marketplace m on ma.marketplaceId=m.id join MarketplaceHasShop ahs on m.id=ahs.marketplaceId where m.type ="marketplace" and ahs.shopId='.$shopId;
            $res = \Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
            foreach ($res as $result) {
                $marketplaceHasShop[]=['id'=>$result['id'],
                    'name'=>$result['name'],
                    'marketplaceAccountId'=>$result['marketplaceAccountId'],
                    'marketplaceId'=>$result['marketplaceId'],
                    'isActive'=>$result['isActive'],
                    'typeMarketplace'=>$result['typeMarketplace']];
            }
            $shop->marketplaceHasShop=$marketplaceHasShop;
/*
            $marketplaceHasShop = \Monkey::app()->repoFactory->create('MarketplaceHasShop')->findBy(['shopId' => $shopId]);
            $marketplaceHasshop=[];

            $shop->marketplaceHasShop = $marketplaceHasShop; */
            $campaign = \Monkey::app()->repoFactory->create('Campaign')->findBy(['remoteShopId' => $shopId]);
            $shop->campaign = $campaign;
            $couponEvent = [];

            $sql = 'select ce.id, ce.`name`, ce.`description`,DATE_FORMAT(ce.startDate, "%d-%m-%Y") as startDate,DATE_FORMAT(ce.endDate, "%d-%m-%Y") as endDate,
             if(c.isActive=1,"Attiva","Non Attiva") as isActive,
       ct.name as couponTypeName, 
        if(ct.isActive=1,"Attivo","Non Attivo") as isActiveType,
            ct.id as couponTypeId,
            if(c.name is null,"nessuna campagna","c.name") as campaignName from CouponEvent ce
                
             join CouponType ct on ce.couponTypeId=ct.id
            left join Campaign c on ct.campaignId=c.id
             where ce.remoteShopId=' . $shopId;
            $res = \Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
            foreach ($res as $result) {
                $couponEvent[] = ['id' => $result['id'],'name' => $result['name'],'description' => $result['description'],'startDate' => $result['startDate'],'endDate' => $result['endDate'],'couponTypeName' => $result['couponTypeName'],'couponTypeId' => $result['couponTypeId'],'isActiveType'=>$result['isActiveType'],'campaignName' => $result['campaignName'],'isActive' => $result['isActive']];
            }
            $banner = \Monkey::app()->repoFactory->create('Banner')->findBy(['remoteShopId' => $shopId]);
            $shop->banner=$banner;
            $shop->couponEvent = $couponEvent;
            $shop->productStatistics = $shop->getDailyActiveProductStatistics();
            $shop->orderStatistics = $shop->getDailyOrderFriendStatistics();
            return json_encode($shop);
        } else {
            $this->app->router->response()->raiseUnauthorized();
            return 'Bad Request';
        }
    }

    public
    function put()
    {
        try {
            $shopData = $this->app->router->request()->getRequestData('shop');
            $shopsId = $this->app->repoFactory->create('Shop')->getAutorizedShopsIdForUser();
            if (in_array($shopData['id'],$shopsId)) {
                $shop = $this->app->repoFactory->create('Shop')->findOneByStringId($shopData['id']);
                $shop->title = $shopData['title'];
                $shop->owner = $shopData['owner'];
                $shop->referrerEmails = $shopData['referrerEmails'];
                $shop->eloyApiKey = $shopData['eloyApiKey'];
                $shop->secret = $shopData['secret'];
                $shop->dbHost = $shopData['dbHost'];
                $shop->dbUsername = $shopData['dbUsername'];
                $shop->dbPassword = $shopData['dbPassword'];
                $shop->dbName = $shopData['dbName'];
                $shop->logo = $shopData['logo'];
                $shop->logoThankYou = $shopData['logoThankYou'];
                $shop->paralellFee = $shopData['paralellFee'];
                $shop->feeParallelOrder = $shopData['feeParallelOrder'];
                $shop->billingParallelId = $shopData['billingParallelId'];
                $shop->hasEcommerce = $shopData['hasEcommerce'];
                $shop->hasCoupon = $shopData['hasCoupon'];
                $shop->hasMarketplace = $shopData['hasMarketplace'];
                $shop->receipt = $shopData['receipt'];
                $shop->invoiceUe = $shopData['invoiceUe'];
                $shop->invoiceExtraUe = $shopData['invoiceExtraUe'];
                $shop->invoiceParalUe = $shopData['invoiceParalUe'];
                $shop->invoiceParalExtraUe = $shopData['invoiceParalExtraUe'];
                $shop->siteInvoiceChar = $shopData['siteInvoiceChar'];
                $shop->urlSite = $shopData['urlSite'];
                $shop->analyticsId = $shopData['analyticsId'];
                $shop->emailShop = $shopData['emailShop'];
                $shop->amministrativeEmails = $shopData['amministrativeEmails'];
                $shop->billingEmails = $shopData['billingEmails'];
                $shop->billingContact = $shopData['billingContact'];
                $shop->importer = $shopData['importer'];


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
                    $this->app->dbAdapter->insert('ShopHasShippingAddressBook',['shopId' => $shop->id,'addressBookId' => $shippingAddress->id],false,true);
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
    private
    function getAndFillAddressData($addressBookData)
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