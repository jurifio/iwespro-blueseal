<?php

namespace bamboo\blueseal\jobs;

use bamboo\blueseal\marketplace\prestashop\CPrestashopProduct;
use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CPrestashopHasProduct;
use bamboo\domain\entities\CPrestashopHasProductHasMarketplaceHasShop;
use bamboo\domain\entities\CProductPublicSku;
use bamboo\domain\entities\CProductEan;
use bamboo\domain\entities\CProductSku;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductBrand;
use bamboo\domain\entities\CShop;
use bamboo\domain\entities\CUserAddress;
use bamboo\ecommerce\views\VBase;
use bamboo\blueseal\business\CBlueSealPage;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use PDO;
use prepare;
use AEntity;
use bamboo\domain\entities\CSite;
use bamboo\domain\entities\CUserHasShop;
use bamboo\domain\repositories\CUserAddressRepo;
use bamboo\domain\entities\CUser;
use PDOException;

/**
 * Class CAlignPickySiteOrderPaymentsJob
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 18/09/2019
 * @since 1.0
 */

class CAlignPickySiteOrderPaymentsJob extends ACronJob
{

    /**
     * @param null $args
     * @throws \PrestaShopWebserviceException
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function run($args = null)
    {
        $this -> importOrder();
    }

    /**
     * @throws \PrestaShopWebserviceException
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    private function importOrder()
    {

        set_time_limit(0);
        ini_set('memory_limit', '2048M');

        $res = "";
        $shopRepo = \Monkey ::app() -> repoFactory -> create('Shop') -> findBy(['hasEcommerce' => 1]);

        foreach ($shopRepo as $value) {
            $this -> report('Start ImportOrder From PickySite ', 'Shop To Import' . $value -> name);
            /********marketplace********/
            $db_host = $value -> dbHost;
            $db_name = $value -> dbName;
            $db_user = $value -> dbUsername;
            $db_pass = $value -> dbPassword;
            $shop = $value -> id;
            try {

                $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_user, $db_pass);
                $db_con -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $res .= " connessione ok <br>";
            } catch (PDOException $e) {
                $res .= $e -> getMessage();
            }


            $userRepo = \Monkey ::app() -> repoFactory -> create('User');
            $userDetailsRepo = \Monkey ::app() -> repoFactory -> create('UserDetails');
            $userEmailRepo = \Monkey ::app() -> repoFactory -> create('UserEmail');
            $getUserIdRepo = \Monkey ::app() -> repoFactory -> create('User');
            $userAddressRepo = \Monkey ::app() -> repoFactory -> create('UserAddress');

            $cartRepo = \Monkey ::app() -> repoFactory -> create('Cart');

            $orderRepo = \Monkey ::app() -> repoFactory -> create('Order');
            $orderLineRepo = \Monkey ::app() -> repoFactory -> create('OrderLine');

            $couponRepo = \Monkey ::app() -> repoFactory -> create('Coupon');


            $productSkuRepo = \Monkey ::app() -> repoFactory -> create('ProductSku');



            try {
                /***inserimento ordini */
                $stmtOrder = $db_con->prepare('SELECT 
                                               o.id as remoteId,
                                               o.orderPaymentMethodId as orderPaymentMethodId,
                                               o.orderShippingMethodId as orderShippingMethodId,
                                               o.couponId as couponId,
                                               o.userId as userId,
                                               U.email as email, 
                                               o.cartId as cartId,
                                               `o`.`status` as `status`,
                                               o.frozenBillingAddress as frozenBillingAddress,
                                               o.frozenShippingAddress as frozenShippingAddress,
                                               o.billingAddressId as billingAddressId,
                                               o.shipmentAddressId as shipmentAddressId,
                                               o.shippingPrice as shippingPrice,
                                               o.userDiscount as userDiscount,
                                               o.couponDiscount as couponDiscount,
                                               o.paymentModifier as paymentModifier,
                                               o.grossTotal as grossTotal,
                                               o.netTotal as netTotal,
                                               o.vat as vat,
                                               o.sellingFee as sellingFee,
                                               o.customModifier as customModifier,
                                               o.orderDate as orderDate,
                                               o.note as note,
                                               o.shipmentNote as shipmentNote,
                                               o.transactionNumber as transactionNumber,
                                               o.transactionMac as transactionMac,
                                               o.paidAmount as paidAmount,
                                               o.paymentDate as paymentDate,
                                               o.lastUpdate as lastUpdate,
                                               o.creationDate as creationDate,
                                               o.hasInvoice as hasInvoice
                                               from `Order` o join User U on o.userId = U.id WHERE  o.orderPaymentMethodId in (1,2)');
                $stmtOrder->execute();
                while ($rowOrder = $stmtOrder->fetch(PDO::FETCH_ASSOC)) {
                    $updateOrder = $orderRepo->findOneBy(['remoteId' => $rowOrder['remoteId'], 'remoteShopId' => $shop]);

                    if ($updateOrder == null) {
                        $updatedOrderId = 'H' . '-' . $updateOrder->id;
                        $updateOrder->shippingPrice = $rowOrder['shippingPrice'];
                        $updateOrder->userDiscount = $rowOrder['userDiscount'];
                        $updateOrder->couponDiscount = $rowOrder['couponDiscount'];
                        $updateOrder->paymentModifier = 0 - $rowOrder['paymentModifier'];
                        $updateOrder->grossTotal = $rowOrder['grossTotal'];
                        $updateOrder->netTotal = $rowOrder['netTotal'];
                        $updateOrder->vat = $rowOrder['vat'];
                        $updateOrder->sellingFee = $rowOrder['sellingFee'];
                        $updateOrder->customModifier = $rowOrder['customModifier'];
                        $updateOrder->orderDate = $rowOrder['orderDate'];
                        $updateOrder->note = $rowOrder['note'];
                        $updateOrder->transactionNumber = $rowOrder['transactionNumber'];
                        $updateOrder->transactionMac = $rowOrder['transactionMac'];
                        $updateOrder->paidAmount = $rowOrder['paidAmount'];
                        $updateOrder->paymentDate = $rowOrder['paymentDate'];
                        $updateOrder->lastUpdate = $rowOrder['lastUpdate'];


                        $updateOrder->update();
                        $this->report('Update Payment CAlignPickySiteOrderPaymentsJob', 'update Order:' . $updatedOrderId . 'shop :' . $value->name);


                    } else {

                        continue;

                    }
                }

            } catch (\throwable $e) {
                $this->report('Update Payment CAlignPickySiteOrderPaymentsJob', 'error', 'Errore Order Update Payments' . $e);
            }

            $this -> report('Finish Align Update Payments Orders with Paypal and Credit Card ', 'Shop:' . $value -> name);
        }





        $this -> report('Finish Procedure Order ', 'End Procedure');


    }


}