<?php

namespace bamboo\blueseal\jobs;

use bamboo\blueseal\marketplace\prestashop\CPrestashopProduct;
use bamboo\core\base\CObjectCollection;
use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CPrestashopHasProduct;
use bamboo\domain\entities\CPrestashopHasProductHasMarketplaceHasShop;
use bamboo\domain\entities\CProductPublicSku;
use PDO;

/**
 * Class CPrestashopAlignProductFeature
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 11/07/2019
 * @since 1.0
 */
class CPrestashopAlignProductFeature extends ACronJob
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
        $this->updateFeature();
        $this->report('Update Feature product Prestashop', 'start job Update');
    }

    /**
     * @throws \PrestaShopWebserviceException
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    private function updateFeature()
    {
        $db_host = "5.189.159.187";
        $db_name = "iwesPrestaDB";
        $db_user = "iwesprestashop";
        $db_pass = "X+]l&LEa]zSI";
        $res = "";
        try {

            $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_user, $db_pass);
            $db_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $res .= " connessione ok <br>";
        } catch (PDOException $e) {
            $res .= $e->getMessage();
            $this->report('Update Feature product Prestashop', 'error connection Update');
        }
        // Extraxt Value from Iwes
        $productRepo = \Monkey::app()->repoFactory->create('Product');
        $productSheetActualRepo = \Monkey::app()->repoFactory->create('ProductSheetActual');
        $productDetailRepo = \Monkey::app()->repoFactory->create('ProductDetail');
        $productDetailTranslationRepo = \Monkey::app()->repoFactory->create('ProductDetailTranslation');
        $productDetailLabelRepo = \Monkey::app()->repoFactory->create('ProductDetailLabel');
        $productDetailLabelTranslationRepo = \Monkey::app()->repoFactory->create('ProductDetailLabelTranslation');

        //collect All label from  Iwes
        $productDetailLabel = $productDetailLabelRepo->findAll();
        $positionDetail = 0;
        foreach ($productDetailLabel as $productDetailLabels) {
            //insert or update Feature Ids in Prestashop with ids Detail Labels Iwes
            $stmtUpdateFeature = $db_con->prepare("INSERT INTO ps_feature (`id_feature`,`position`) VALUES
                                                                                      ('" . $productDetailLabels->id . "',
                                                                                       '" . $positionDetail . "') 
                                                                                       ON DUPLICATE KEY UPDATE
                                                                                       ('" . $productDetailLabels->id . "',
                                                                                       '" . $positionDetail . "')

                                            ");
            $stmtUpdateFeature->execute();
            //Collect from Pickyshop array shop in order to populate label detail from picky
            $stmtGetShop = $db_con->prepare("SELECT `id_shop` from ps_shop");
            $stmtGetShop->execute();
            while ($rowGetShop = $stmtGetShop->fetch(PDO::FETCH_ASSOC)) {
                $stmtUpdateFeatureShop = $db_con->prepare("INSERT INTO ps_feature_shop (`id_feature`,`id_shop`) VALUES
                                                                                      ('" . $productDetailLabels->id . "',
                                                                                       '" . $rowGetShop['id_shop'] . "') 
                                                                                       ON DUPLICATE KEY UPDATE
                                                                                       ('" . $productDetailLabels->id . "',
                                                                                       '" . $rowGetShop['id_shop'] . "')

                                            ");
                $stmtUpdateFeatureShop->execute();
            }
            //collect Lang Label Detail from Picky in Italian Language by productDetailLabelsId
            $productDetailLabelTranslationIt = $productDetailLabelTranslationRepo->findBy(['productDetailLabelId' => $productDetailLabels->id, 'langId' => 1]);
            //Verify if objectcollection is not null
            if ($productDetailLabelTranslationIt != null) {
                //define value to insert or update feature_lang  in prestashop with Italian Language
                $id_feature = $productDetailLabels->id;
                $id_lang = 1;
                $name = $productDetailLabelTranslationIt->name;
                //insert or update Value in Table
                $stmtUpdateFeatureLangIt = $db_con->prepare("INSERT INTO ps_feature_lang(`id_feature`,`id_lang`,`name`) VALUES
                                                                                        ('" . $id_feature . "',
                                                                                        '" . $id_lang . "',
                                                                                        '" . $name . "')
                                                                                        ON DUPLICATE KEY UPDATE
                                                                                       ('" . $id_feature . "',
                                                                                        '" . $id_lang . "',
                                                                                        '" . $name . "')
  ");
                $stmtUpdateFeatureLangIt->execute();
            }
            //collect Lang Label Detail from Picky in English Language by productDetailLabelsId
            $productDetailLabelTranslationEn = $productDetailLabelTranslationRepo->findOneBy(['productDetailLabelId' => $productDetailLabel->id, 'langId' => 2]);
            //Verify if objectcollection is not null
            if ($productDetailLabelTranslationEn != null) {
                //define value to insert or update feature_lang  in prestashop with English Language
                $id_feature = $productDetailLabels->id;
                $id_lang = 2;
                $name = $productDetailLabelTranslationEn->name;
                //insert or update Value in Table
                $stmtUpdateFeatureLangEn = $db_con->prepare("INSERT INTO ps_feature_lang (`id_feature`,`id_lang`,`name`) VALUES
                                                                                        ('" . $id_feature . "',
                                                                                        '" . $id_lang . "',
                                                                                        '" . $name . "')
                                                                                        ON DUPLICATE KEY UPDATE
                                                                                       ('" . $id_feature . "',
                                                                                        '" . $id_lang . "',
                                                                                        '" . $name . "')
  ");
                $stmtUpdateFeatureLangEn->execute();


            }
            //collect Lang Label Detail from Picky in Deutch Language by productDetailLabelsId
            $productDetailLabelTranslationDe = $productDetailLabelTranslationRepo->findOneBy(['productDetailLabelId' => $productDetailLabel->id, 'langId' => 3]);
            //Verify if objectcollection is not null
            if ($productDetailLabelTranslationDe != null) {
                //define value to insert or update feature_lang  in prestashop with English Language
                $id_feature = $productDetailLabels->id;
                $id_lang = 3;
                $name = $productDetailLabelTranslationDe->name;
                //insert or update Value in Table
                $stmtUpdateFeatureLangDe = $db_con->prepare("INSERT INTO ps_feature_lang(`id_feature`,`id_lang`,`name`) VALUES
                                                                                        ('" . $id_feature . "',
                                                                                        '" . $id_lang . "',
                                                                                        '" . $name . "')
                                                                                        ON DUPLICATE KEY UPDATE
                                                                                       ('" . $id_feature . "',
                                                                                        '" . $id_lang . "',
                                                                                        '" . $name . "')
  ");
                $stmtUpdateFeatureLangDe->execute();

            }
            $positionDetail++;
        }
        $productDetail = $productDetailRepo->findAll();

        foreach ($productDetail as $productDetails) {
            //insert or update Feature Value Ids in Prestashop with ids Detail Iwes
            $stmtUpdateFeatureValue = $db_con->prepare("INSERT INTO ps_feature_value (`id_feature_value`,`id_feature`,`custom`) VALUES
                                                                                      ('" . $productDetails->id . "',
                                                                                      ' 1 ',
                                                                                       '0') 
                                                                                       ON DUPLICATE KEY UPDATE
                                                                                        ('" . $productDetails->id . "',
                                                                                      ' 1 ',
                                                                                       '0')

                                            ");
            $stmtUpdateFeatureValue->execute();

            //collect Lang  Detail from Picky in Italian Language by productDetailId
            $productDetailTranslationIt = $productDetailTranslationRepo->findBy(['productDetailId' => $productDetails->id, 'langId' => 1]);
            //Verify if objectcollection is not null
            if ($productDetailTranslationIt != null) {
                //define value to insert or update feature_value_lang  in prestashop with Italian Language
                $id_feature_value = $productDetails->id;
                $id_lang = 1;
                $name = $productDetailTranslationIt->name;
                //insert or update Value in Table
                $stmtUpdateFeatureValueLangIt = $db_con->prepare("INSERT INTO ps_feature_value_lang(`id_feature_value`,`id_lang`,`value`) VALUES
                                                                                        ('" . $id_feature_value . "',
                                                                                        '" . $id_lang . "',
                                                                                        '" . $name . "')
                                                                                        ON DUPLICATE KEY UPDATE
                                                                                       ('" . $id_feature_value . "',
                                                                                        '" . $id_lang . "',
                                                                                        '" . $name . "')
  ");
                $stmtUpdateFeatureValueLangIt->execute();
            }
            //collect Lang  Detail value from Picky in English Language by productDetailId
            $productDetailTranslationEn = $productDetailTranslationRepo->findOneBy(['productDetailLabelId' => $productDetailLabel->id, 'langId' => 2]);
            //Verify if objectcollection is not null
            if ($productDetailTranslationEn != null) {
                //define value to insert or update feature_value_lang  in prestashop with English Language
                $id_feature_value = $productDetails->id;
                $id_lang = 2;
                $name = $productDetailTranslationEn->name;
                //insert or update Value in Table
                $stmtUpdateFeatureValueLangEn = $db_con->prepare("INSERT INTO ps_feature_value_lang (`id_feature_value`,`id_lang`,`value`) VALUES
                                                                                          ('" . $id_feature_value . "',
                                                                                        '" . $id_lang . "',
                                                                                        '" . $name . "')
                                                                                        ON DUPLICATE KEY UPDATE
                                                                                       ('" . $id_feature_value . "',
                                                                                        '" . $id_lang . "',
                                                                                        '" . $name . "')
  ");
                $stmtUpdateFeatureValueLangEn->execute();


            }
            //collect Lang  Detail from Picky in Deutch Language by productDetailId
            $productDetailTranslationDe = $productDetailTranslationRepo->findOneBy(['productDetailId' => $productDetailLabel->id, 'langId' => 3]);
            //Verify if objectcollection is not null
            if ($productDetailTranslationDe != null) {
                //define value to insert or update feature_value_lang  in prestashop with English Language
                $id_feature_value = $productDetails->id;
                $id_lang = 3;
                $name = $productDetailTranslationDe->name;
                //insert or update Value in Table
                $stmtUpdateFeatureValueLangDe = $db_con->prepare("INSERT INTO ps_feature_value_lang(`id_feature_value`,`id_lang`,`value`) VALUES
                                                                                          ('" . $id_feature_value . "',
                                                                                        '" . $id_lang . "',
                                                                                        '" . $name . "')
                                                                                        ON DUPLICATE KEY UPDATE
                                                                                       ('" . $id_feature_value . "',
                                                                                        '" . $id_lang . "',
                                                                                        '" . $name . "')
  ");
                $stmtUpdateFeatureValueLangDe->execute();

            }

        }


        $prestashopHasProductRepo = \Monkey::app()->repoFactory->create('PrestashopHasProduct');
        $phpC = \Monkey::app()->repoFactory->create('ProductSheetActual')->findAll();
        foreach ($phpC as $php) {
            $findPrestaId = $prestashopHasProductRepo->findOneBy(['productId' => $php->productId, 'productVariantId' => $php->productVariantId]);
            $prestaId = $findPrestaId->prestaId;
            $productId = $php->productId;
            $productVariantId = $php->productVariantId;
            $stmtUpdateFeatureProduct = $db_con->prepare("INSERT INTO ps_feature_product (`id_feature`,`id_product`,`id_feature_value`) VALUES
                                                                                        ('" . $php->productDetailLabelId . "',
                                                                                        '" . $prestaId . "',
                                                                                        '" . $php->productDetailId . "')
                                                                                        ON DUPLICATE KEY UPDATE
                                                                                       ('" . $id_feature_value . "',
                                                                                        '" . $id_lang . "',
                                                                                        '" . $name . "')
  ");
            $stmtUpdateFeatureProduct->execute();


            $this->report('Update Feature product Prestashop', 'End Update');
        }
    }
}