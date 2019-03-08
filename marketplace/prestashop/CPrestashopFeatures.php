<?php


namespace bamboo\blueseal\marketplace\prestashop;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CProductDetail;
use bamboo\domain\entities\CProductDetailLabel;
use bamboo\domain\entities\CProductDetailsHasPrestashopFeatures;


/**
 * Class CPrestashopFeatures
 * @package bamboo\blueseal\marketplace\prestashop
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 27/02/2019
 * @since 1.0
 */
class CPrestashopFeatures extends APrestashopMarketplace
{

    CONST FEATURE_RESOURCE = 'product_features';
    CONST FEATURE_VALUE_RESOURCE = 'product_feature_values';

    /**
     * @param $productDetailsLabel
     * @return bool
     * @throws BambooException
     * @throws \PrestaShopWebserviceException
     */

    public function addNewFeatures($productDetailsLabel): bool
    {

        //if argument is object create objectCollection and then iterate it
        if ($productDetailsLabel instanceof CProductDetailLabel) {
            $singleProductDetailLabel = $productDetailsLabel;

            unset($productDetailsLabel);
            $productDetailsLabel = new CObjectCollection();
            $productDetailsLabel->add($singleProductDetailLabel);
        }

        /** @var CProductDetailLabel $productDetailLabel */
        foreach ($productDetailsLabel as $productDetailLabel) {

            //first of all if label not have detail go forward
            $pickyDetails = $productDetailLabel->getAssociatedDetails(true);
            if(!$pickyDetails) continue;


                try {

                    //Search feature -> if not exist insert feature and values
                    if (!$this->checkIfExistDetailLabel($productDetailLabel)) {

                        /** @var \SimpleXMLElement $blankFeatureXml */
                        $blankFeatureXml = $this->getBlankSchema($this::FEATURE_RESOURCE);

                        $resourceFeature = $blankFeatureXml->children()->children();

                        $resourceFeature->name->language[0][0] = $productDetailLabel->productDetailLabelTranslation->isEmpty() ? $productDetailLabel->slug : $productDetailLabel->getLocalizedName();

                        $optFeature = array('resource' => $this::FEATURE_RESOURCE);
                        $optFeature['postXml'] = $blankFeatureXml->asXML();
                        $optFeature['id_group_shop'] = 5;
                        $responseFeature = $this->ws->add($optFeature);

                        //if succesfull added then save feature
                        if ($responseFeature instanceof \SimpleXMLElement) {
                            $prestashopFeatureId = (int)$responseFeature->children()->children()->id[0];
                        } else throw new BambooException('Prestashop response ProductFeatureValue error');


                        /** @var CProductDetail $pickyDetail */
                        foreach ($pickyDetails as $pickyDetail) {

                            $featureValue = $pickyDetail->productDetailTranslation->isEmpty() ? $pickyDetail->slug : $pickyDetail->getLocalizedDetail();
                            if(empty($featureValue)) continue;

                            //search if detail is already in matching table
                            if (!$this->checkIfExistDetail($productDetailLabel, $pickyDetail)) {

                                /** @var \SimpleXMLElement $blankFeatureValueXml */
                                $blankFeatureValueXml = $this->getBlankSchema($this::FEATURE_VALUE_RESOURCE);

                                $resourceFeatureValue = $blankFeatureValueXml->children()->children();

                                $resourceFeatureValue->id_feature = $prestashopFeatureId;
                                $resourceFeatureValue->value->language[0][0] = $featureValue;

                                $optFeatureValue = array('resource' => $this::FEATURE_VALUE_RESOURCE);
                                $optFeatureValue['postXml'] = $blankFeatureValueXml->asXML();
                                $responseFeatureValue = $this->ws->add($optFeatureValue);

                                if ($responseFeatureValue instanceof \SimpleXMLElement) {
                                    $prestashopFeatureValueId = (int)$responseFeatureValue->children()->children()->id[0];

                                    /** @var CProductDetailsHasPrestashopFeatures $pdhpfNew */
                                    $pdhpfNew = \Monkey::app()->repoFactory->create('ProductDetailsHasPrestashopFeatures')->getEmptyEntity();
                                    $pdhpfNew->productDetailLabelId = $productDetailLabel->id;
                                    $pdhpfNew->productDetailId = $pickyDetail->id;
                                    $pdhpfNew->prestashopFeatureId = $prestashopFeatureId;
                                    $pdhpfNew->prestashopFeatureValueId = $prestashopFeatureValueId;
                                    $pdhpfNew->smartInsert();

                                } else throw new BambooException('Prestashop response ProductFeatureValue error');
                            } else continue;

                        }

                    }


                } catch (\Throwable $e) {
                    \Monkey::app()->applicationLog('PrestashopFeature', 'Error', 'Errore while insert', $e->getMessage());
                    return false;
                }

        }

        return true;
    }

    /**
     * @param CProductDetailLabel $productDetailLabel
     * @return bool
     * @throws BambooException
     * @throws \PrestaShopWebserviceException
     */
    public function checkIfExistDetailLabel(CProductDetailLabel $productDetailLabel)
    {
        //check if data are consistent between Prestashop database and Pickyshop database
        /** @var CProductDetailsHasPrestashopFeatures $existInPrestashop */
        $existInPrestashop = \Monkey::app()->repoFactory->create('ProductDetailsHasPrestashopFeatures')->findOneBy(['productDetailLabelId' => $productDetailLabel->id]);

        if (!is_null($existInPrestashop)) {

            $featureExist = $this->getResourceFromId($existInPrestashop->prestashopFeatureId, $this::FEATURE_RESOURCE);

            if (empty($featureExist->children()->children())) {
                \Monkey::app()->applicationLog('ProductDetailsHasPrestashopFeatures (FEATURE)', 'Error', 'Dangerous error while try to insert feature (product detail label)', $productDetailLabel->id . ' on Pickyshop database but not in Prestashop database');
                throw new BambooException($productDetailLabel->id . ' on Pickyshop database but not in Prestashop database');
            }
            return true;
        }

        return false;
    }

    /**
     * @param CProductDetailLabel $productDetailLabel
     * @param CProductDetail $productDetail
     * @return bool
     * @throws BambooException
     * @throws \PrestaShopWebserviceException
     */
    public function checkIfExistDetail(CProductDetailLabel $productDetailLabel, CProductDetail $productDetail)
    {
        //check if data are consistent between Prestashop database and Pickyshop database

        //first search detail with specific label and if exist return true and go forward
        /** @var CProductDetailsHasPrestashopFeatures $existInPrestashop */
        $existInPrestashop = \Monkey::app()->repoFactory->create('ProductDetailsHasPrestashopFeatures')->findOneBy([
            'productDetailLabelId' => $productDetailLabel->id,
            'productDetailId' => $productDetail->id
        ]);

        if (!is_null($existInPrestashop)) {
            $featureExist = $this->getResourceFromId($existInPrestashop->prestashopFeatureValueId, $this::FEATURE_VALUE_RESOURCE);

            if (empty($featureExist->children()->children())) {
                \Monkey::app()->applicationLog('ProductDetailsHasPrestashopFeatures (FEATURE VALUE)', 'Error', 'Dangerous error while try to insert feature value (product detail)', $productDetailLabel->id . ' on Pickyshop database (with specific label) but not in Prestashop database');
                throw new BambooException($productDetailLabel->id . ' on Pickyshop (with specific label) database but not in Prestashop database');
            }

            return true;
        }

        return false;
    }


    /**
     * @param CProductDetailLabel $productDetailLabel
     * @param array $fields
     * @param array $opt
     * @return bool
     * @throws \PrestaShopWebserviceException
     */
    public function updatePrestashopFeature(CProductDetailLabel $productDetailLabel, array $fields, array $opt = [])
    {

        if (isset($opt['resource']) || isset($opt['putXml']) || isset($opt['id'])) return false;


        $id = \Monkey::app()->repoFactory->create('ProductDetailsHasPrestashopFeatures')->findOneBy(['productDetailLabelId' => $productDetailLabel->id])->prestashopFeatureId;

        $xml = $this->getResourceFromId($id, $this::FEATURE_RESOURCE);
        $resources = $xml->children()->children();

        if (!empty($fields)) {
            foreach ($fields as $nameField => $valueField) {
                $resources->{$nameField} = $valueField;
            }
        }


        //set static opt
        $opt['resource'] = $this::FEATURE_RESOURCE;
        $opt['putXml'] = $xml->asXML();
        $opt['id'] = $id;

        //set passed opt
        $xml = $this->ws->edit($opt);

        if ($xml instanceof \SimpleXMLElement) return true;

        return false;
    }

}