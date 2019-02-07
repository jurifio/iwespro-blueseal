<?php


namespace bamboo\blueseal\marketplace\prestashop;
use bamboo\controllers\api\Helper\DateTime;
use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\theming\nestedCategory\CCategoryManager;
use bamboo\domain\entities\CProductCategory;
use bamboo\domain\entities\CProductCategoryHasPrestashopCategory;
use bamboo\domain\entities\CProductCategoryTranslation;


/**
 * Class CPrestashopCategory
 * @package bamboo\blueseal\remote\prestashopmarketplace
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 23/01/2019
 * @since 1.0
 */
class CPrestashopCategory extends APrestashopMarketplace
{

    /**
     * @return \SimpleXMLElement
     * @throws \PrestaShopWebserviceException
     */
    public function getCategoryBlankSchema() : \SimpleXMLElement
    {
        $xml = $this->ws->get(array('resource' => 'categories'));
        return $xml;
    }

    /**
     * @param CObjectCollection $productCategories
     * @return bool
     * @throws \PrestaShopWebserviceException
     */
    public function addAllCategories(CObjectCollection $productCategories)
    {
        /** @var \SimpleXMLElement $blankXml */
        $blankXml = $this->getCategoryBlankSchema();

        $counter = 0;
        /** @var CCategoryManager $catManager */
        $catManager = \Monkey::app()->categoryManager;
        /** @var CProductCategory $productCategory */
        foreach ($productCategories as $productCategory){

            //root category
            if ($counter == 0){
                $blankXml[0]->children()[$counter]->id_parent = $catManager->getCategoryParent($productCategory->id)['id'];
                $blankXml[0]->children()[$counter]->active = 1;
                $blankXml[0]->children()[$counter]->id_shop_default = 1;
                $blankXml[0]->children()[$counter]->is_root_category = 0;
                $blankXml[0]->children()[$counter]->position = 0;
                $blankXml[0]->children()[$counter]->date_add = '2019-01-21 17:53:51';
                $blankXml[0]->children()[$counter]->date_upd = '2019-01-21 17:53:51';
                $blankXml[0]->children()[$counter]->name->language[0][0] = $productCategory->productCategoryTranslation->findOneByKey('langId', 1)->name;
                $blankXml[0]->children()[$counter]->link_rewrite->language[0][0] = $productCategory->productCategoryTranslation->findOneByKey('langId', 1)->slug;
                $blankXml[0]->children()[$counter]->description->language[0][0] = $productCategory->productCategoryTranslation->findOneByKey('langId', 1)->name;
                $blankXml[0]->children()[$counter]->meta_title->language[0][0] = $productCategory->productCategoryTranslation->findOneByKey('langId', 1)->name;
                $blankXml[0]->children()[$counter]->meta_description->language[0][0] = $productCategory->productCategoryTranslation->findOneByKey('langId', 1)->name;
                $blankXml[0]->children()[$counter]->meta_keywords->language[0][0] = $productCategory->productCategoryTranslation->findOneByKey('langId', 1)->name;
                $counter++;
                continue;
            }

            $dom_thing = dom_import_simplexml($blankXml);
            $dom_node  = dom_import_simplexml($blankXml->category);
            $dom_new   = $dom_thing->appendChild($dom_node->cloneNode(true));
            $new_node  = simplexml_import_dom($dom_new);

            $blankXml[0]->children()[$counter]->id_parent = $catManager->getCategoryParent($productCategory->id)['id'];
            $blankXml[0]->children()[$counter]->active = 1;
            $blankXml[0]->children()[$counter]->id_shop_default = 1;
            $blankXml[0]->children()[$counter]->is_root_category = 0;
            $blankXml[0]->children()[$counter]->position = 0;
            $blankXml[0]->children()[$counter]->date_add = '2019-01-21 17:53:51';
            $blankXml[0]->children()[$counter]->date_upd = '2019-01-21 17:53:51';
            $blankXml[0]->children()[$counter]->name->language[0][0] = $productCategory->productCategoryTranslation->findOneByKey('langId', 1)->name;
            $blankXml[0]->children()[$counter]->link_rewrite->language[0][0] = $productCategory->productCategoryTranslation->findOneByKey('langId', 1)->slug;
            $blankXml[0]->children()[$counter]->description->language[0][0] = $productCategory->productCategoryTranslation->findOneByKey('langId', 1)->name;
            $blankXml[0]->children()[$counter]->meta_title->language[0][0] = $productCategory->productCategoryTranslation->findOneByKey('langId', 1)->name;
            $blankXml[0]->children()[$counter]->meta_description->language[0][0] = $productCategory->productCategoryTranslation->findOneByKey('langId', 1)->name;
            $blankXml[0]->children()[$counter]->meta_keywords->language[0][0] = $productCategory->productCategoryTranslation->findOneByKey('langId', 1)->name;
            $counter++;
        }

        $opt = array('resource' => 'categories');
        $opt['postXml'] = $blankXml->asXML();
        $response = $this->ws->add($opt);

        return true;
    }

    /**
     * @param CObjectCollection $productCategories
     * @throws \PrestaShopWebserviceException
     */
    public function addNewCategories(CObjectCollection $productCategories){

        /** @var CRepo $pchpcR */
        $pchpcR = \Monkey::app()->repoFactory->create('ProductCategoryHasPrestashopCategory');

        /** @var CCategoryManager $categoryManager */
        $categoryManager = \Monkey::app()->categoryManager;

        /** @var CProductCategory $productCategory */
        foreach ($productCategories as $productCategory){

            //search category in table
            /** @var CProductCategoryHasPrestashopCategory $pchpc */
            $pchpc = $pchpcR->findOneBy(['productCategoryId' => $productCategory->id]);

            //if i found category is in prestashop
            if(!is_null($pchpc)) continue;

            $fatherId = $categoryManager->getCategoryParent($productCategory->id)['id'];

            /** @var CProductCategoryHasPrestashopCategory $pchpcFather */
            $pchpcFather = $pchpcR->findOneBy(['productCategoryId' => $fatherId]);
            if(!is_null($pchpcFather)){
                //if father is in table insert new cat
                $this->insertPrestashopCategory($productCategory, $pchpcFather);
            } else {

            }

        }
    }

    /**
     * @param CProductCategory $productCategory
     * @param CProductCategoryHasPrestashopCategory $pchpcFather
     * @return bool
     * @throws \PrestaShopWebserviceException
     */
    public function insertPrestashopCategory(CProductCategory $productCategory, CProductCategoryHasPrestashopCategory $pchpcFather){

        /** @var \SimpleXMLElement $blankXml */
        $blankXml = $this->getCategoryBlankSchema();

        /** @var CProductCategoryTranslation $productCategoryTranslation */
        $productCategoryTranslation = $productCategory->productCategoryTranslation->findOneByKey('langId', 1);

        $date = date_format(new \DateTime(), 'Y-m-d H:i:s');
        $categorySlug = $productCategoryTranslation->slug;
        $categoryName = $productCategoryTranslation->name;
        

        $blankXml[0]->children()->id_parent = $pchpcFather->prestashopCategoryId;
        $blankXml[0]->children()->active = 1;
        $blankXml[0]->children()->id_shop_default = 1;
        $blankXml[0]->children()->is_root_category = 0;
        $blankXml[0]->children()->position = 0;
        $blankXml[0]->children()->date_add = $date;
        $blankXml[0]->children()->date_upd = $date;
        $blankXml[0]->children()->name->language[0][0] = $categoryName;
        $blankXml[0]->children()->link_rewrite->language[0][0] = $categorySlug;
        $blankXml[0]->children()->description->language[0][0] = $categoryName;
        $blankXml[0]->children()->meta_title->language[0][0] = $categoryName;
        $blankXml[0]->children()->meta_description->language[0][0] = $categoryName;
        $blankXml[0]->children()->meta_keywords->language[0][0] = $categoryName;

        $opt = array('resource' => 'categories');
        $opt['postXml'] = $blankXml->asXML();
        $response = $this->ws->add($opt);

        return true;
    }
}