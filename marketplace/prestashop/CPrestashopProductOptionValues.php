<?php


namespace bamboo\blueseal\marketplace\prestashop;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CProductBrand;
use bamboo\domain\entities\CProductBrandHasPrestashopManufacturer;
use bamboo\domain\entities\CProductColorGroup;


/**
 * Class CPrestashopProductOptionValues
 * @package bamboo\blueseal\marketplace\prestashop
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 14/02/2019
 * @since 1.0
 */
class CPrestashopProductOptionValues extends APrestashopMarketplace
{

    CONST PRESTASHOP_SIZE = 1;
    CONST PRESTASHOP_COLOR = 1;

    /**
     * @param $productColorGroups
     * @return bool
     */
    public function addNewColorsOptionValues($productColorGroups): bool
    {
        //if argument is object create objectCollection and then iterate it
        if ($productColorGroups instanceof CProductColorGroup) {
            $singleProductColorGroup = $productColorGroups;

            unset($productColorGroups);
            $productColorGroups = new CObjectCollection();
            $productColorGroups->add($singleProductColorGroup);
        }

       // $prestashopShop = new CPrestashopShop();
       // $shopIds = $prestashopShop->getAllPrestashopShops();

        /** @var CProductColorGroup $productColorGroup */
        foreach ($productColorGroups as $productColorGroup) {

          //  foreach ($shopIds as $shopId) {
                try {

                    if (!$this->checkIfExistColor($productColorGroup)) {

                        /** @var \SimpleXMLElement $blankXml */
                        $blankXml = $this->getBlankSchema();

                        $resources = $blankXml->children()->children();

                        $resources->id_attribute_group = $this::PRESTASHOP_COLOR;
                        $resources->name->language[0][0] = $productColorGroup->name;
                        $resources->color = $productColorGroup->hex;

                        $opt = array('resource' => $this->resource);
                        $opt['postXml'] = $blankXml->asXML();
                        $response = $this->ws->add($opt);


                        //if ($response instanceof \SimpleXMLElement) {
                        //    $prestashopManufacturerId = (int)$response->children()->children()->id[0];

                            /** @var CProductBrandHasPrestashopManufacturer $pbhpmNew */
                        //    $pbhpmNew = \Monkey::app()->repoFactory->create('ProductBrandHasPrestashopManufacturer')->getEmptyEntity();
                        //    $pbhpmNew->productBrandId = $productBrand->id;
                        //    $pbhpmNew->prestashopManufacturerId = $prestashopManufacturerId;
                        //    $pbhpmNew->smartInsert();
                        //} else throw new BambooException('Prestashop response ProductCategory error');

                    }


                } catch (\Throwable $e) {
                    \Monkey::app()->applicationLog('PrestashopManufacturers', 'Error', 'Errore while insert', $e->getMessage());
                    return false;
                }
           // }
        }

        return true;
    }

    /**
     * @param CProductColorGroup $productColorGroup
     * @return bool
     * @throws BambooException
     * @throws \PrestaShopWebserviceException
     */
    public function checkIfExistColor(CProductColorGroup $productColorGroup)
    {
        //check if data are consistent between Prestashop database and Pickyshop database
        $existInPrestashop = \Monkey::app()->repoFactory->create('ProductColorGroupHasPrestashopColorOption')->findOneBy(['productColorGroupId'=>$productColorGroup->id]);

        if (!is_null($existInPrestashop)) {
            $colorOptionExist = $this->getResourceFromId($productColorGroup->productColorGroupHasPrestashopColorOption->prestashopColorOptionValueId);

            if (empty($colorOptionExist->children()->children())) {
                \Monkey::app()->applicationLog('PrestashopProductOptionValues', 'Error', 'Dangerous error while try to insert color', $productColorGroup->id . ' on Pickyshop database but not in Prestashop database');
                throw new BambooException($productColorGroup->id . ' on Pickyshop database but not in Prestashop database');
            }
            return true;
        }

        return false;
    }

    /**
     * @param CProductBrand $productBrand
     * @return bool
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function deletePrestahopManufacturer(CProductBrand $productBrand): bool
    {
        try {
            $this->ws->delete(array('resource' => $this->resource, 'id' => $productBrand->productBrandHasPrestashopManufacturer->prestashopManufacturerId));
        } catch (\Throwable $e) {
            \Monkey::app()->applicationLog('PrestashopBrand', 'Error', 'Error while deleting', $e->getMessage());
        }


        /** @var CRepo $prodBrHPrestaManRepo */
        $prodBrHPrestaManRepo = \Monkey::app()->repoFactory->create('ProductBrandHasPrestashopManufacturer');

        /** @var CProductBrandHasPrestashopManufacturer $prodBrHPrestaMan */
        $prodBrHPrestaMan = $prodBrHPrestaManRepo->findOneBy(['productBrandId' => $productBrand->id]);
        $prodBrHPrestaMan->delete();

        return true;
    }

    /**
     * @param CProductBrand $productBrand
     * @param array $fields
     * @param array $opt
     * @return bool
     * @throws \PrestaShopWebserviceException
     */

    public function updatePrestashopManufacturer(CProductBrand $productBrand, array $fields, array $opt = [])
    {

        if (isset($opt['resource']) || isset($opt['putXml']) || isset($opt['id'])) return false;

        $id = $productBrand->productBrandHasPrestashopManufacturer->prestashopManufacturerId;

        $xml = $this->getManufacturer($id);
        $resources = $xml->children()->children();

        if (!empty($fields)) {
            foreach ($fields as $nameField => $valueField) {
                $resources->{$nameField} = $valueField;
            }
        }

        unset($resources->link_rewrite);

        //set static opt
        $opt['resource'] = $this->resource;
        $opt['putXml'] = $xml->asXML();
        $opt['id'] = $id;

        //set passed opt
        $xml = $this->ws->edit($opt);

        return true;

    }
}