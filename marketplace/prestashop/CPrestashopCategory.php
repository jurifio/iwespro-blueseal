<?php


namespace bamboo\blueseal\marketplace\prestashop;
use bamboo\controllers\api\Helper\DateTime;
use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\core\theming\nestedCategory\CCategoryManager;
use bamboo\core\utils\slugify\CSlugify;
use bamboo\domain\entities\CProductCategory;
use bamboo\domain\entities\CProductCategoryHasPrestashopCategory;
use bamboo\domain\entities\CProductCategoryTranslation;
use bamboo\domain\repositories\CProductCategoryRepo;
use bamboo\domain\repositories\CProductRepo;


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
        $xml = $this->ws->get(array('resource' => 'categories/?schema=blank'));
        return $xml;
    }

    /**
     * @param $productCategories
     * @return bool
     * @throws \Exception
     */
    public function addNewCategories($productCategories) : bool{

        //if argument is object create objectCollection and then iterate it
        if($productCategories instanceof CProductCategory){
            $singleProductCategory = $productCategories;

            unset($productCategories);
            $productCategories = new CObjectCollection();
            $productCategories->add($singleProductCategory);
        }

        /** @var CRepo $pchpcR */
        $pchpcR = \Monkey::app()->repoFactory->create('ProductCategoryHasPrestashopCategory');

        /** @var CCategoryManager $categoryManager */
        $categoryManager = \Monkey::app()->categoryManager;

        /** @var CProductCategoryRepo $productCategoryRepo */
        $productCategoryRepo = \Monkey::app()->repoFactory->create('ProductCategory');

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
                if($this->insertPrestashopCategory($productCategory, $pchpcFather)){
                    continue;
                } else throw new \Exception('Errore while insert Product Category');
            } else {
                $fatherTree = $this->getRecursiveFatherProductCategory($fatherId, true);

                for($i = 0; $i < count($fatherTree['fathers']); $i++){

                    /** @var CProductCategory $prodCat */
                    $prodCat = $productCategoryRepo->findOneBy(['id' => $fatherTree['fathers'][$i]]);

                    if($i === 0){
                        /** @var CProductCategoryHasPrestashopCategory $pchpcFatherFirst */
                        $pchpcFatherFirst = $pchpcR->findOneBy(['productCategoryId' => $fatherTree['lastFatherProductCategoryId']]);
                        if($this->insertPrestashopCategory($prodCat, $pchpcFatherFirst)){
                            continue;
                        } else throw new \Exception('Errore while insert Product Category');
                    }

                    /** @var CProductCategoryHasPrestashopCategory $previousFather */
                    $previousFather = $pchpcR->findOneBy(['productCategoryId' => $fatherTree['fathers'][$i-1]]);
                    if($this->insertPrestashopCategory($prodCat, $previousFather)){
                        continue;
                    } else throw new \Exception('Errore while insert Product Category');
                }

                //child insert
                /** @var CProductCategoryHasPrestashopCategory $lastChildFather */
                $lastChildFather = $pchpcR->findOneBy(['productCategoryId' => end($fatherTree['fathers'])]);
                if($this->insertPrestashopCategory($productCategory, $lastChildFather)){
                    continue;
                } else throw new \Exception('Errore while insert Product Category');
            }

        }

        return true;
    }

    /**
     * @param $productCategoryId
     * @param bool $init
     * @param array $res
     * @return array
     */
    private function getRecursiveFatherProductCategory($productCategoryId, bool $init, array $res = []) : array {

        if($init) $res[] = $productCategoryId;

        /** @var CRepo $pchpcRepo */
        $pchpcRepo = \Monkey::app()->repoFactory->create('ProductCategoryHasPrestashopCategory');

        /** @var CCategoryManager $cm */
        $cm = \Monkey::app()->categoryManager;

        $grandFatherId = $cm->getCategoryParent($productCategoryId)['id'];

        /** @var CProductCategoryHasPrestashopCategory $pchpcGF */
        $pchpcGF = $pchpcRepo->findOneBy(['productCategoryId' => $grandFatherId]);

        if(is_null($pchpcGF)){
            $res[] = $grandFatherId;
            return $this->getRecursiveFatherProductCategory($grandFatherId, false, $res);
        }

        $res['fathers'] = array_reverse($res);
        $res['lastFatherProductCategoryId'] = $pchpcGF->productCategoryId;

        return $res;
    }

    /**
     * @param CProductCategory $productCategory
     * @param CProductCategoryHasPrestashopCategory $pchpcFather
     * @return bool
     */
    private function insertPrestashopCategory(CProductCategory $productCategory, CProductCategoryHasPrestashopCategory $pchpcFather) : bool {

        try {

            $slugy = new CSlugify();

            /** @var \SimpleXMLElement $blankXml */
            $blankXml = $this->getCategoryBlankSchema();

            $resources = $blankXml->children()->children();
            /** @var CProductCategoryTranslation $productCategoryTranslation */
            $productCategoryTranslation = $productCategory->productCategoryTranslation->findOneByKey('langId', 1);

            $date = date_format(new \DateTime(), 'Y-m-d H:i:s');
            $categorySlug = (is_null($productCategoryTranslation->slug) || empty($productCategoryTranslation->slug)) ? $slugy->slugify(trim($productCategoryTranslation->name)) : $productCategoryTranslation->slug;
            $categoryName = $productCategoryTranslation->name;


            $resources->id_parent = $pchpcFather->prestashopCategoryId;
            $resources->active = 1;
            $resources->id_shop_default = 1;
            $resources->is_root_category = 0;
            unset($resources->position);
            $resources->date_add = $date;
            $resources->date_upd = $date;
            $resources->name->language[0][0] = $categoryName;
            $resources->link_rewrite->language[0][0] = $categorySlug;
            $resources->description->language[0][0] = $categoryName;
            $resources->meta_title->language[0][0] = $categoryName;
            $resources->meta_description->language[0][0] = $categoryName;
            $resources->meta_keywords->language[0][0] = $categoryName;

            $opt = array('resource' => 'categories');
            $opt['postXml'] = $blankXml->asXML();
            $response = $this->ws->add($opt);


            if ($response instanceof \SimpleXMLElement) {
                $prestashopCategoryId = (int)$response->children()->children()[0];

                /** @var CProductCategoryHasPrestashopCategory $pchpcNew */
                $pchpcNew = \Monkey::app()->repoFactory->create('ProductCategoryHasPrestashopCategory')->getEmptyEntity();
                $pchpcNew->productCategoryId = $productCategory->id;
                $pchpcNew->prestashopCategoryId = $prestashopCategoryId;
                $pchpcNew->smartInsert();
            } else throw new BambooException('Prestashop response ProductCategory error');
        } catch (\Throwable $e){
            \Monkey::app()->applicationLog('PrestashopCategory', 'Error', 'Errore while insert', $e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * @param CProductCategory $productCategory
     * @return array
     * @throws BambooException
     * @throws \PrestaShopWebserviceException
     */
    public function deletePrestahopCategory(CProductCategory $productCategory) : array {

        /** @var CProductCategoryHasPrestashopCategory $productCategoryHasPrestashopCategory */
        $productCategoryHasPrestashopCategory = $productCategory->productCategoryHasPrestashopCategory;

        $prestashopCategoryId = $productCategoryHasPrestashopCategory->prestashopCategoryId;
        $prestashopShop = new CPrestashopShop();

        $shopIds = $prestashopShop->getAllPrestashopShops();

        $res = [];
        foreach ($shopIds as $shopId) {
            try{
                $this->ws->delete(array('resource' => 'categories', 'id' => $prestashopCategoryId, 'id_shop' => $shopId));
                $res['deleted'][] = $shopId;
            } catch (\Throwable $e){
                $res['notDeleted'][] = $shopId;
                \Monkey::app()->applicationLog('PrestashopCategory', 'Error', 'Error while deleting', $e->getMessage());
                break;
            }
        }

        if(empty($res['notDeleted'])){
            $cm = \Monkey::app()->categoryManager;

            $childIds = $cm->categories()->childrenIds($productCategory->id);

            foreach ($childIds as $childId){

                /** @var CRepo $prodCatHPrestaCatRepo */
                $prodCatHPrestaCatRepo = \Monkey::app()->repoFactory->create('ProductCategoryHasPrestashopCategory');

                /** @var CProductCategoryHasPrestashopCategory $prdCatHasPresCat */
                $prdCatHasPresCat = $prodCatHPrestaCatRepo->findOneBy(['productCategoryId'=>$childId]);
                $prdCatHasPresCat->delete();
            }

            $productCategoryHasPrestashopCategory->delete();
        }

        return $res;
    }
}