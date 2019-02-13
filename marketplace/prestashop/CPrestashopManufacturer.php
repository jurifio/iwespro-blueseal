<?php


namespace bamboo\blueseal\marketplace\prestashop;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CProductBrand;
use bamboo\domain\entities\CProductBrandHasPrestashopManufacturer;
use bamboo\domain\entities\CProductCategory;
use bamboo\domain\entities\CProductCategoryHasPrestashopCategory;


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
        $xml = $this->ws->get(array('resource' => 'manufacturers/?schema=blank'));
        return $xml;
    }

    /**
     * @param $productBrands
     * @return bool
     */
    public function addNewManufacturers($productBrands): bool
    {
        //if argument is object create objectCollection and then iterate it
        if ($productBrands instanceof CProductBrand) {
            $singleProductCategory = $productBrands;

            unset($productBrands);
            $productBrands = new CObjectCollection();
            $productBrands->add($singleProductCategory);
        }

        /** @var CProductBrand $productBrand */
        foreach ($productBrands as $productBrand) {

            try {

                if ($this->checkIfExistManufacturer($productBrand)) continue;

                /** @var \SimpleXMLElement $blankXml */
                $blankXml = $this->getManufacturesBlankSchema();

                $resources = $blankXml->children()->children();

                $resources->active = 1;
                $resources->name = $productBrand->name;
                $resources->description->language[0][0] = $productBrand->name;;
                $resources->short_description->language[0][0] = $productBrand->name;;
                $resources->meta_title->language[0][0] = $productBrand->name;;
                $resources->meta_description->language[0][0] = $productBrand->name;;
                $resources->meta_keywords->language[0][0] = $productBrand->name;;

                $opt = array('resource' => 'manufacturers');
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

            } catch (\Throwable $e) {
                \Monkey::app()->applicationLog('PrestashopManufacturers', 'Error', 'Errore while insert', $e->getMessage());
                return false;
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
        if (!is_null($productBrand->productBrandHasPrestashopManufacturer)) {
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
}