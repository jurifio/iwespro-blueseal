<?php


namespace bamboo\blueseal\marketplace\prestashop;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CProductBrand;
use bamboo\domain\entities\CProductBrandHasPrestashopManufacturer;


/**
 * Class CPrestashopManufacturer
 * @package bamboo\blueseal\marketplace\prestashop
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 13/02/2019
 * @since 1.0
 */
class CPrestashopManufacturer extends APrestashopMarketplace
{

    /**
     * @return \SimpleXMLElement
     * @throws \PrestaShopWebserviceException
     */
    public function getManufacturesBlankSchema(): \SimpleXMLElement
    {
        $xml = $this->ws->get(array('resource' => $this->resource . '/?schema=blank'));
        return $xml;
    }

    /**
     * @param $productBrands
     * @return bool
     * @throws BambooException
     * @throws \PrestaShopWebserviceException
     */
    public function addNewManufacturers($productBrands): bool
    {
        //if argument is object create objectCollection and then iterate it
        if ($productBrands instanceof CProductBrand) {
            $singleProductBrand = $productBrands;

            unset($productBrands);
            $productBrands = new CObjectCollection();
            $productBrands->add($singleProductBrand);
        }

        $prestashopShop = new CPrestashopShop();
        $shopIds = $prestashopShop->getAllPrestashopShops();

        /** @var CProductBrand $productBrand */
        foreach ($productBrands as $productBrand) {

            foreach ($shopIds as $shopId) {
                try {

                    if (!$this->checkIfExistManufacturer($productBrand)) {

                        /** @var \SimpleXMLElement $blankXml */
                        $blankXml = $this->getManufacturesBlankSchema();

                        $resources = $blankXml->children()->children();

                        $resources->active = 1;
                        $resources->name = $productBrand->name;
                        $resources->description->language[0][0] = $productBrand->name;
                        $resources->short_description->language[0][0] = $productBrand->name;
                        $resources->meta_title->language[0][0] = $productBrand->name;
                        $resources->meta_description->language[0][0] = $productBrand->name;
                        $resources->meta_keywords->language[0][0] = $productBrand->name;

                        $opt = array('resource' => $this->resource, 'id_shop' => $shopId);
                        $opt['postXml'] = $blankXml->asXML();
                        $response = $this->ws->add($opt);


                        if ($response instanceof \SimpleXMLElement) {
                            $prestashopManufacturerId = (int)$response->children()->children()->id[0];

                            /** @var CProductBrandHasPrestashopManufacturer $pbhpmNew */
                            $pbhpmNew = \Monkey::app()->repoFactory->create('ProductBrandHasPrestashopManufacturer')->getEmptyEntity();
                            $pbhpmNew->productBrandId = $productBrand->id;
                            $pbhpmNew->prestashopManufacturerId = $prestashopManufacturerId;
                            $pbhpmNew->smartInsert();
                        } else throw new BambooException('Prestashop response ProductCategory error');

                    } else {
                        $opt = [];
                        $opt['id_shop'] = $shopId;
                        $this->updatePrestashopManufacturer($productBrand, [], $opt);
                }


                } catch (\Throwable $e) {
                    \Monkey::app()->applicationLog('PrestashopManufacturers', 'Error', 'Errore while insert', $e->getMessage());
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param int $prestashopManufacturerId
     * @return \SimpleXMLElement
     * @throws \PrestaShopWebserviceException
     */
    public function getManufacturer(int $prestashopManufacturerId): \SimpleXMLElement
    {
        return $this->ws->get(array('resource' => $this->resource, 'id' => $prestashopManufacturerId));
    }

    /**
     * @param CProductBrand $productBrand
     * @return bool
     * @throws BambooException
     * @throws \PrestaShopWebserviceException
     */
    public function checkIfExistManufacturer(CProductBrand $productBrand)
    {
        //check if data are consistent between Prestashop database and Pickyshop database

        $existInPrestashop = \Monkey::app()->repoFactory->create('ProductBrandHasPrestashopManufacturer')->findOneBy(['productBrandId'=>$productBrand->id]);

        if (!is_null($existInPrestashop)) {
            $manufacturerExist = $this->getManufacturer($productBrand->productBrandHasPrestashopManufacturer->prestashopManufacturerId);

            if (empty($manufacturerExist->children()->children())) {
                \Monkey::app()->applicationLog('PrestashopManufacturer', 'Error', 'Dangerous error while try to insert manufacturer', $productBrand->id . ' on Pickyshop database but not in Prestashop database');
                throw new BambooException($productBrand->id . ' on Pickyshop database but not in Prestashop database');
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