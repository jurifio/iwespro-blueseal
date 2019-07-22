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


/**
 * Class CPrestashopUpdateProductDescription
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
class CPrestashopUpdateProductDescription extends ACronJob
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
        $this->updatePrestashopDescription();
    }

    /**
     * @throws \PrestaShopWebserviceException
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    private function updatePrestashopProductDescription()
    {
        /** @var CPrestashopHasProduct $phpRepo */
        $phpRepo = \Monkey::app()->repoFactory->create('PrestashopHasProduct');
        $productRepo = \Monkey::app()->repoFactory->create('Product');
        $productDetailsRepo = \Monkey::app()->repoFactory->create('ProductDetails');

        $productSheetModelActualRepo = \Monkey::app()->repoFactory->create('ProductSheetModelActual');
        $productInPrestashop = $phpRepo ->findAll();
        foreach ($productInPrestashop as $pips) {
            if ($pips->prestaId != null) {
                /** @var CProductSheetActual $productSheetModelActual */
                $productSheetModelActual = $productSheetModelActualRepo->findBy(['productId' => $pips->productId, 'productVariantId' => $pips->productVariantid]);
                if ($productSheetModelActual != null) {
                    $descriptionLabelTranslationIt='';
                    foreach ($productSheetModelActual as $psma) {
                        if($psma->productDetailLabel->productDetailLabelTranslation->langId==1){
                            $labelFeatureIt=$psma->productDetailLabel->name;
                        }
                        if($psma->productDetail->productDetailTranslation->langId==1){
                            $detailFeatureIt=$psma->productDetail->productDetailTranslation->name;
                        }
                        $descriptionLabelTranslationIt.=$labelFeatureIt.":".$detailFeatureIt."</br>";
                    }
                }
                $this->report('update  Feature product Prestashop', 'ProductId: '.$pips->prestaId. ' Details: '.$descriptionLabelTranslationIt);
            }
        }
        $this->report('Update Feature product Prestashop', 'End Update');
    }
}