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
        $this->updatePrestashopProductDescription();
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
        $productDetailRepo = \Monkey::app()->repoFactory->create('ProductDetail');
        $productDetailLabelRepo =\Monkey::app()->repoFactory->create('ProductDetailLabel');
        $productDetailTranslationRepo = \Monkey::app()->repoFactory->create('ProductDetailTranslation');
        $productDetailLabelTranslationRepo =\Monkey::app()->repoFactory->create('ProductDetailLabelTranslation');


        $productSheetActualRepo = \Monkey::app()->repoFactory->create('ProductSheetActual');
        $productInPrestashop = $phpRepo ->findAll();
        foreach ($productInPrestashop as $pips) {
            if ($pips->prestaId != null) {
                /** @var CProductSheetActual $productSheetModelActual */
                $productSheetActual = $productSheetActualRepo->findBy(['productId' => $pips->productId, 'productVariantId' => $pips->productVariantId]);
                if ($productSheetActual != null) {
                    $descriptionTranslationIt='';
                    $descriptionTranslationEn='';
                    $descriptionTranslationDe='';
                    $labelFeatureIt='';
                    $labelFeatureEn='';
                    $labelFeatureDe='';
                    $detailFeatureIt='';
                    $detailFeatureEn='';
                    $detailFeatureDe='';
                    foreach ($productSheetActual as $psma) {
                            $producDetailLabelId=$psma->productDetailLabelId;
                            $productDetailId=$psma->productDetailId;
                            $productDetailLabelTranslation=$productDetailLabelTranslationRepo->findBy(['productDetailLabelId'=>$producDetailLabelId]);
                            if($productDetailLabelTranslation!=null) {
                                foreach ($productDetailLabelTranslation as $pdlt) {
                                    if ($pdlt->langId == 1) {
                                        $labelFeatureIt = $pdlt->name;
                                    }else{
                                        $labelFeatureIt='';
                                    }
                                    if ($pdlt->langId == 2) {
                                        $labelFeatureEn = $pdlt->name;
                                    }else{
                                        $labelFeatureEn='';
                                    }
                                    if ($pdlt->langId == 3) {
                                        $labelFeatureDe = $pdlt->name;
                                    }else{
                                        $labelFeatureDe='';
                                    }
                                }
                            }
                        $productDetailTranslation=$productDetailTranslationRepo->findBy(['productDetailId'=>$productDetailId]);
                            if($productDetailTranslation!=null) {
                                foreach ($productDetailTranslation as $pdt) {
                                    if ($pdt->langId == 1) {
                                        $detailFeatureIt = $pdt->name;
                                    }else{
                                        $detailFeatureIt='';
                                    }
                                    if ($pdt->langId == 2) {
                                        $detailFeatureEn = $pdlt->name;
                                    }else{
                                        $detailFeatureEn='';
                                    }
                                    if ($pdlt->langId == 3) {
                                        $detailFeatureDe = $pdlt->name;
                                    }else{
                                        $detailFeatureDe='';
                                    }
                                }
                            }
                        $descriptionTranslationIt.=$labelFeatureIt.":".$detailFeatureIt."</br>";
                        $descriptionTranslationEn.=$labelFeatureEn.":".$detailFeatureEn."</br>";
                        $descriptionTranslationDe.=$labelFeatureDe.":".$detailFeatureDe."</br>";
                    }
                }
                $this->report('update  Feature product Prestashop', 'ProductId: '.$pips->prestaId. ' Details: '.$descriptionTranslationIt);
                $this->report('update  Feature product Prestashop', 'ProductId: '.$pips->prestaId. ' Details: '.$descriptionTranslationEn);
                $this->report('update  Feature product Prestashop', 'ProductId: '.$pips->prestaId. ' Details: '.$descriptionTranslationDe);
            }
        }
        $this->report('Update Feature product Prestashop', 'End Update');
    }
}