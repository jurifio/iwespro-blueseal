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
use bamboo\domain\entities\CDirtySkuHasStoreHouse;
use bamboo\domain\repositories\CUserAddressRepo;
use bamboo\domain\entities\CUser;
use PDOException;


class CTagProductWithStockHouseJob extends ACronJob
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
        $this->tagProduct();
    }

    /**
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    private function tagProduct()
    {

        set_time_limit(0);
        ini_set('memory_limit','2048M');

        $res = "";
        $shopRepo = \Monkey::app()->repoFactory->create('Shop')->findBy(['hasEcommerce' => 1]);
        $storehouseRepo = \Monkey::app()->repoFactory->create('Storehouse');
        $dirtySkuHaStoreHouseRepo = \Monkey::app()->repoFactory->create('dirtySkuHasStoreHouse');

        foreach ($shopRepo as $value) {
            $this->report('CTagProductWithStockHouseJob','Shop To Import' . $value->name);
            /********marketplace********/
            $db_host = $value->dbHost;
            $db_name = $value->dbName;
            $db_user = $value->dbUsername;
            $db_pass = $value->dbPassword;
            $shop = $value->id;
            try {

                $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
                $db_con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                $res .= " connessione ok <br>";
            } catch (PDOException $e) {
                $res .= $e->getMessage();
            }

            $storeHouses=$storehouseRepo->findBy(['shopId'=>$shop]);
            foreach($storeHouses as $store) {
                $storeTag = str_replace(' ','',$store->name);
                try {
                    $stmtFindTag = $db_con->prepare('SELECT id as tagId, count(*) as countTag from Tag where slug="' . $storeTag . '"');
                    $stmtFindTag->execute();
                    $rowFindTag = $stmtFindTag->fetchAll(PDO::FETCH_ASSOC);
                    if ($rowFindTag[0]['countTag'] != 0) {
                        $tagId = $rowFindTag[0]['tagId'];
                    } else {
                        $stmtInsertTag = $db_con->prepare('INSERT INTO Tag (slug,isPublic,sortingPriorityId) values("' . $storeTag . '",0,999)');
                        $stmtInsertTag->execute();
                        $tagId = $db_con->lastInsertId();
                        $stmInsertTagTranslation = $db_con->prepare('INSERT INTO TagTranslation (tagId, langId,`name`)VALUES("' . $tagId . '",1,"' . $store->name . '")');
                        $stmInsertTagTranslation->execute();

                    }

                } catch (\Throwable $e) {
                    \Monkey::app()->applicationReport('CTagProductWithStockHouseJob','error','Cannot manageTag',$e->getLine().'-'.$e->getMessage());
                }

                try {
                    $collect = $dirtySkuHaStoreHouseRepo->findBy(['shopId' => $shop,'storeHouseId' => $store->id]);
                    foreach ($collect as $pr) {
                        $stmtFindPrTag = $db_con->prepare('SELECT count(*) as prExist FROM ProductHasTag 
                    where productId=' . $pr->productId . ' and productVariantId=' . $pr->productVariantId . ' and tagId=' . $tagId);
                        $stmtFindPrTag->execute();
                        $rowFindPrTag = $stmtFindPrTag->fetchAll(PDO::FETCH_ASSOC);
                        if ($rowFindPrTag[0]['prExist'] != 0) {
                            continue;
                        } else {
                            $stmtInsertPrTag = $db_con->prepare('INSERT INTO ProductHasTag (productId,productVariantId,tagId,position) values(' . $pr->productId . ',' . $pr->productVariantId . ',' . $tagId . ',null)');
                            $stmtInsertPrTag->execute();
                        }

                    }
                }
                catch(\Throwable $e){
                    \Monkey::app()->applicationReport('CTagProductWithStockHouseJob','error','Cannot InsertTag',$e->getLine().'-'.$e->getMessage());
                }

                \Monkey::app()->applicationReport('Finish Procedure TagProductWithStockHouseJob','End Procedure','','');

            }
        }
    }

}