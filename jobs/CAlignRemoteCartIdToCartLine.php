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
 * Class CAlignRemoteCartIdToCartLine
 * @package bamboo\blueseal\jobs
 */
class CAlignRemoteCartIdToCartLine extends ACronJob
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
        $this->alignCartLine();
    }

    /**
     * @throws \PrestaShopWebserviceException
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    private function alignCartLine()
    {

        set_time_limit(0);
        ini_set('memory_limit','2048M');

        $res = "";
        $shopRepo = \Monkey::app()->repoFactory->create('Shop')->findBy(['hasEcommerce' => 1]);
        $orderRepo = \Monkey::app()->repoFactory->create('Order');
        $cartRepo=\Monkey::app()->repoFactory->create('Order');
        $cartLineRepo=\Monkey::app()->repoFactory->create('OrdeLine');
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

            $carts = $cartRepo -> findBy(['remoteShopSellerId' => $shop]);
            foreach ($carts as $cart) {
                $stmtSelectCartLine = $db_con -> prepare("select id, productId,productVariantId,productSizeId  from CartLine where cartId=" . $cart -> remoteCartSellerId . " and isParallel is null");
                $stmtSelectCartLine -> execute();
                while ($rowSelectCartLine = $stmtSelectCartLine -> fetch(PDO::FETCH_ASSOC)) {
                    $cartLine = $cartLineRepo -> findOneBy(['productId' => $rowSelectCartLine['productId'],
                        'productVariantId' => $rowSelectCartLine['productVariantId'],
                        'productSizeId' => $rowSelectCartLine['productSizeId'],
                        'cartId' => $cart -> id, 'remoteShopSellerId' => $shop]);
                    if ($cartLine == null) {
                        $cartLineInsert = \Monkey ::app() -> repoFactory -> create('CartLine') -> getEmptyEntity();
                        $cartLineInsert -> productId = $rowSelectCartLine['productId'];
                        $cartLineInsert -> productVariantId = $rowSelectCartLine['productVariantId'];
                        $cartLineInsert -> productSizeId = $rowSelectCartLine['productSizeId'];
                        $cartLineInsert -> remoteCartLineSellerId = $rowSelectCartLine['id'];
                        $cartLineInsert -> remoteCartSellerId = $cart -> remoteCartSellerId;
                        $cartLineInsert -> insert();
                    } else {
                        $cartLine -> remoteCartSellerId = $cart -> remoteCartSellerId;
                        $cartLine -> update();

                    }

                }
            }
        }
    }

}