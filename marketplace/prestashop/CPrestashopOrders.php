<?php


namespace bamboo\blueseal\marketplace\prestashop;

use bamboo\controllers\api\Helper\DateTime;
use bamboo\domain\entities\CUser;
use bamboo\domain\entities\CUserAddress;
use bamboo\domain\repositories\CExternalUserRepo;
use bamboo\domain\repositories\COrderRepo;
use bamboo\domain\repositories\CUserAddressRepo;
use bamboo\domain\repositories\CUserRepo;

/**
 * Class CPrestashopOrders
 * @package bamboo\blueseal\marketplace\prestashop
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 10/04/2019
 * @since 1.0
 */
class CPrestashopOrders extends APrestashopMarketplace
{

    CONST ORDER_RESOURCE = 'orders';

    private $reposUtility;

    public function insertOrderInPicky($from = null, $to = null)
    {

        $ordersInfo = $this->getOrders($from, $to);

        /** @var COrderRepo $orderRepo */
        $orderRepo = \Monkey::app()->repoFactory->create('Order');

        foreach ($ordersInfo as $orderInfo) {

            try {
                \Monkey::app()->repoFactory->beginTransaction();
                if ($orderRepo->findOneBy(['prestashopOrderId' => (int)$orderInfo['order']->id])) continue;

                /** @var CUser $user */
                $user = $this->insertPrestashopUserInPicky($orderInfo['customer']);

                $addresses = $this->insertPrestashopUserAddressInPicky($user, $orderInfo['addressDelivery'], $orderInfo['addressInvoice']);

                $orderObect = $orderRepo->getEmptyEntity();
                $orderObect->userId = $user->id;
                $orderObect->status = 'ORD_WAIT';
                $orderObect->frozenBillingAddress = $addresses['billingAddress']->froze();
                $orderObect->frozenShippingAddress = $addresses['shipmentAddress']->froze();
                $orderObect->billingAddressId = $addresses['billingAddress']->id;
                $orderObect->shipmentAddressId = $addresses['shipmentAddress']->id;


                \Monkey::app()->repoFactory->commit();
            } catch (\Throwable $e) {
                \Monkey::app()->repoFactory->rollback();
            }
        }

    }

    public function getOrders($from = null, $to = null): array
    {

        if (is_null($from) || is_null($to)) {
            $today = new \DateTime();
            $from = $today->format('Y-m-d 00:00:00');
            $to = $today->format('Y-m-d 23:59:59');
        }

        $orders = $this->getDataFromResource(self::ORDER_RESOURCE, null, ["date_add" => "[$from,$to]"], null, 1, null, 1);

        $orderInfo = [];
        foreach ($orders->children()->children() as $orderAttributeId) {
            $orderId = (int)$orderAttributeId->attributes();

            $order = $this->getDataFromResource(self::ORDER_RESOURCE, $orderId, [], null, 1)->children()->children();
            $addressDelivery = $this->getAddress((int)$order->id_address_delivery);
            $addressInvoice = $this->getAddress((int)$order->id_address_invoice);
            $customer = $this->getCustomer((int)$order->id_customer);
            $orderInfo[(int)$order->id]['order'] = $order;
            $orderInfo[(int)$order->id]['addressDelivery'] = $addressDelivery;
            $orderInfo[(int)$order->id]['addressInvoice'] = $addressInvoice;
            $orderInfo[(int)$order->id]['customer'] = $customer;
        }

        return $orderInfo;
    }

    public function getAddress($addressId)
    {
        return $this->getDataFromResource('addresses', $addressId)->children()->children();
    }

    public function getCustomer($customerId)
    {
        return $this->getDataFromResource('customers', $customerId)->children()->children();
    }

    /**
     * @param \SimpleXMLElement $customer
     * @return CUser
     * @throws \Exception
     * @throws \bamboo\core\exceptions\BambooException
     */
    private function insertPrestashopUserInPicky(\SimpleXMLElement $customer) : CUser
    {
        /** @var CUserRepo $userRepo */
        $userRepo = \Monkey::app()->repoFactory->create('User');

        $gender = ((int)$customer->id_gender) == 1 ? 'M' : 'F';

        /** @var CUser $extUser */
        $extUser = $userRepo->findOneBy(['email' => (string)$customer->email]);

        if(is_null($extUser)) {
            $user = $userRepo->insertUserFromData(
                (string)$customer->email,
                (string)$customer->firstname,
                (string)$customer->lastname,
                (string)$customer->birthday,
                $gender,
                null,
                'Prestashop',
                null,
                null,
                0,
                false
            );

            return $user['user'];
        }

        return $extUser;
    }

    /**
     * @param CUser $user
     * @param \SimpleXMLElement $addressDelivery
     * @param \SimpleXMLElement $addressInvoice
     * @return array
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    private function insertPrestashopUserAddressInPicky(CUser $user, \SimpleXMLElement $addressDelivery, \SimpleXMLElement $addressInvoice){

        /** @var CUserAddressRepo $userAddressRepo */
        $userAddressRepo = \Monkey::app()->repoFactory->create('UserAddress');

        $shippingAddress = $userAddressRepo->insertUserAddressFromData(
            $user->id,
            $user->userDetails->name,
            $user->userDetails->surname,
            (string) $addressDelivery->address1,
            null,
            (string) $addressDelivery->city,
            (string) $addressDelivery->postcode,
            10,
            (string) $addressDelivery->phone,
            null,
            null,
            0,
            false
        );

       $billingAddress = $userAddressRepo->insertUserAddressFromData(
            $user->id,
            $user->userDetails->name,
            $user->userDetails->surname,
            (string) $addressInvoice->address1,
            null,
            (string) $addressInvoice->city,
            (string) $addressInvoice->postcode,
            10,
            (string) $addressInvoice->phone,
            null,
            null,
            0,
            false
        );

       $res = [];
       $res['shippingAddress'] = $shippingAddress;
       $res['billingAddress'] = $billingAddress;

       return $res;
    }
}