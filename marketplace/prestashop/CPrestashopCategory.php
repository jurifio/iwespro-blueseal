<?php


namespace bamboo\blueseal\marketplace\prestashop;
use bamboo\core\base\CObjectCollection;
use bamboo\core\theming\nestedCategory\CCategoryManager;
use bamboo\domain\entities\CProductCategory;


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

    private function duplicateChildNodes($blankXml){
        // Strip it out so it's not passed by reference
        $newNode = new \SimpleXMLElement($blankXml->category->asXML());

        // Create a dummy placeholder for it wherever you need it
        $blankXml->addChild('replaceMe');

        // Do a string replace on the empty fake node
        $blankXml = str_replace('<replaceMe/>',$newNode->asXML(),$blankXml->asXML());
        $xml = str_replace('<?xml version="1.0"?>', '', $blankXml);

    }
}