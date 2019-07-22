<?php

namespace bamboo\blueseal\jobs;

use bamboo\blueseal\marketplace\prestashop\CPrestashopProduct;
use bamboo\core\base\CObjectCollection;
use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CPrestashopHasProduct;
use bamboo\domain\entities\CPrestashopHasProductHasMarketplaceHasShop;
use bamboo\domain\entities\CProductPublicSku;
use bamboo\domain\entities\CProductSheetActual;
use bamboo\domain\entities\CProductDetail;
use bamboo\domain\entities\CProductDetailLabel;
use bamboo\domain\entities\CProductDetailTranslation;
use bamboo\domain\entities\CProductDetailLabelTranslation;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductSku;
use PDO;


/**
 * Class CPrestashopUpdateProductEan
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 22/07/2019
 * @since 1.0
 */
class CPrestashopUpdateProductEan extends ACronJob
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
        $this->updatePrestashopProductEan();
    }

    /**
     * @throws \PrestaShopWebserviceException
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    private function updatePrestashopProductEan()
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
        } catch ( PDOException $e ) {
            $res .= $e->getMessage();
        }


        /** @var CPrestashopHasProduct $phpRepo */
        $phpRepo = \Monkey::app()->repoFactory->create('PrestashopHasProduct');
        $productRepo = \Monkey::app()->repoFactory->create('Product');
        $productDetailRepo = \Monkey::app()->repoFactory->create('ProductDetail');
        $productDetailLabelRepo = \Monkey::app()->repoFactory->create('ProductDetailLabel');
        $productDetailTranslationRepo = \Monkey::app()->repoFactory->create('ProductDetailTranslation');
        $productDetailLabelTranslationRepo = \Monkey::app()->repoFactory->create('ProductDetailLabelTranslation');
        $productEanRepo = \Monkey::app()->repoFactory->create('ProductEan');
        $productSkuRepo = \Monkey::app()->repoFactory->create('ProductSku');
        $prestashopHasProductRepo = \Monkey::app()->repoFactory->create('PrestashopHasProduct');


        $productInPrestashop = $phpRepo->findAll();
        foreach ($productInPrestashop as $pips) {
            if ($pips->prestaId != null) {

                /** @var CProductEan $productEan */
                $productEanParent = $productEanRepo->findOneBy(['productId' => $pips->productId, 'productVariantId' => $pips->productVariantId, 'usedForParen' => 1]);
                if ($productEanParent != null) {
                    $reference = $pips->productId.'-'.$pips->productVariantId;
                    $eanParent = $productEanParent->ean;
                    try {
                        $stmtUpdateProductParentEan = $db_con->prepare("update ps_product set ean13 = '" . $eanParent . "'
                                                                          where reference ='" . $reference . "' and   id_product=" . $pips->prestaId);
                        $stmtUpdateProductParentEan->execute();
                        $this->report('update Ean ps_product Prestashop', 'ProductId: ' . $pips->prestaId . ' Ean Applied: ' . $eanParent);
                    } catch ( \PDOException $e ) {
                        $this->report('Error update Ean ps_product  Prestashop', "update ps_product set ean13 = '" . $productEanParent . "'
                                                                          where reference='" . $reference . "' and   id_product=" . $pips->prestaId);
                    }
                    /** @var CProductSku $productSku */
                    $productSku = $productSkuRepo->findBy(['productId' => $pips->productId, 'productVariantId' => $pips->productVariantId]);
                    if ($productSku != null) {
                        foreach ($productSku as $skus) {
                            $referenceCombination = $skus->productId . "-" . $skus->productVariantId . "-" . $skus->productSizeId;
                            $productEanCombination = $productEanRepo->findOneBy(['productId' => $pips->productId, 'productVariantId' => $pips->productVariantId, 'productSizeId' => $pips->productSizeId]);
                            if ($productEanCombination != null) {
                                $eanCombination = $productEanCombination->ean;
                                try {
                                    $stmtUpdateProductAttributeEan = $db_con->prepare("update ps_product_attribute set ean13 = '" . $eanCombination . "'
                                                                          where  reference='" . $referenceCombination . "'");
                                    $stmtUpdateProductAttributeEan->execute();
                                    $this->report('update Ean ps_product_attribute Prestashop', 'ProductId: ' . $pips->prestaId . ' productSku: ' . $referenceCombination . ' Ean Applied: ' . $eanParent);
                                } catch ( \PDOException $e ) {
                                    $this->report('Error update Ean ps_product_attribute  Prestashop', "update ps_product_attribute set ean13 = '" . $eanCombination . "'
                                                                          where  reference ='" . $referenceCombination . "'");
                                }
                            }
                        }
                    }
                }
            }
        }
        $this->report('Update product  Ean Prestashop', 'End Update');
    }
}