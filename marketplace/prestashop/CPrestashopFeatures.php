<?php


namespace bamboo\blueseal\marketplace\prestashop;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\entities\ILocalizedEntity;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CProductColorGroup;
use bamboo\domain\entities\CProductColorGroupHasPrestashopColorOption;
use bamboo\domain\entities\CProductDetail;
use bamboo\domain\entities\CProductDetailLabel;
use bamboo\domain\entities\CProductDetailsHasPrestashopFeatures;
use bamboo\domain\entities\CProductSize;


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
        //prepare all repository
        /** @var CRepo $pDHPf */
        $pDHPf = \Monkey::app()->repoFactory->create('ProductDetailsHasPrestashopFeatures');

        //if argument is object create objectCollection and then iterate it
        if ($productDetailsLabel instanceof CProductDetailLabel) {
            $singleProductDetailLabel = $productDetailsLabel;

            unset($productColorGroups);
            $productDetailsLabel = new CObjectCollection();
            $productDetailsLabel->add($singleProductDetailLabel);
        }

        $prestashopShop = new CPrestashopShop();
        $shopIds = $prestashopShop->getAllPrestashopShops();

        /** @var CProductDetailLabel $productDetailLabel */
        foreach ($productDetailsLabel as $productDetailLabel) {

            foreach ($shopIds as $shopId) {
                try {

                    //Search feature -> if not exist insert feature and values
                    if (!$this->checkIfExistDetailLabel($productDetailLabel)) {

                        /** @var \SimpleXMLElement $blankFeatureXml */
                        $blankFeatureXml = $this->getBlankSchema('product_features');

                        $resourceFeature = $blankFeatureXml->children()->children();

                        $resourceFeature->name->language[0][0] = $productDetailLabel->getLocalizedName();

                        $opt = array('resource' => $this::FEATURE_RESOURCE);
                        $opt['postXml'] = $blankFeatureXml->asXML();
                        $response = $this->ws->add($opt);

                        //if succesfull added then save feature
                        if ($response instanceof \SimpleXMLElement) {
                            $prestashopFeatureId = (int)$response->children()->children()->id[0];
                        }

                        //get all details and add to prestashop
                        $pickyDetails = $productDetailLabel->getAssociatedDetails();

                        foreach ($pickyDetails as $pickyDetail) {
                            //search if detail is already in matching table
                            if (!$this->checkIfExistDetail($productDetailLabel, $pickyDetail)) {

                            } else continue;

                        }

                        if ($response instanceof \SimpleXMLElement) {
                            $prestashopColorId = (int)$response->children()->children()->id[0];

                            /** @var CProductColorGroupHasPrestashopColorOption $pchpcNew */
                            $pchpcNew = \Monkey::app()->repoFactory->create('ProductColorGroupHasPrestashopColorOption')->getEmptyEntity();
                            $pchpcNew->productColorGroupId = $productDetailLabel->id;
                            $pchpcNew->prestashopColorId = $prestashopColorId;
                            $pchpcNew->smartInsert();
                        } else throw new BambooException('Prestashop response ProductColor error');

                    } else {
                        $opt = [];
                        $opt['id_shop'] = $shopId;
                        $this->updatePrestashopFeature($productDetailLabel, [], $opt);
                    }


                } catch (\Throwable $e) {
                    \Monkey::app()->applicationLog('PrestashopColor', 'Error', 'Errore while insert', $e->getMessage());
                    return false;
                }
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

        //if not exist with the same label search with another to retrive the correct prestashop feature value id
        /** @var CProductDetailsHasPrestashopFeatures $existInPrestashop */
        $existInPrestashop = \Monkey::app()->repoFactory->create('ProductDetailsHasPrestashopFeatures')->findOneBy([
            'productDetailLabelId' => $productDetailLabel->id,
        ]);

        if (!is_null($existInPrestashop)) return $existInPrestashop->prestashopFeatureValueId;

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


        $id = \Monkey::app()->repoFactory->create('ProductDetailsHasPrestashopFeatures')->findOneBy(['productDetailLabelId' => $productDetailLabel->id])->id;

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


    /**
     * @param string $resource
     * @return \SimpleXMLElement
     * @throws \PrestaShopWebserviceException
     */
    public function getBlankSchema(string $resource = ''): \SimpleXMLElement
    {
        return $this->ws->get(array('resource' => $resource . '/?schema=blank'));
    }

    /**
     * @param int $id
     * @param string $resource
     * @return \SimpleXMLElement
     * @throws \PrestaShopWebserviceException
     */
    public function getResourceFromId(int $id, string $resource = ''): \SimpleXMLElement
    {
        return $this->ws->get(array('resource' => $resource, 'id' => $id));
    }

}