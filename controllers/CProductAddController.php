<?php
namespace bamboo\blueseal\controllers;

use bamboo\ecommerce\views\VBase;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class CProductAddController
 * @package bamboo\blueseal\controllers
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CProductAddController extends CProductManageController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "product_add";

    /**
     * @throws \Exception
     * @throws \bamboo\core\exceptions\RedPandaDBALException
     * @throws \bamboo\core\exceptions\RedPandaORMException
     */
    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/product_add.php');

        /** LOGICA */
        $bluesealBase = $this->app->baseUrl(false) . '/blueseal/';
        $fileFolder = $this->app->rootPath().$this->app->cfg()->fetch('paths', 'dummyFolder') . '/';
        $dummyUrl = $this->app->rootPath().$this->app->cfg()->fetch('paths', 'dummyUrl') . '/';
        $elenco = $bluesealBase . "prodotti";
        $nuovoprodotto = $bluesealBase . "prodotti/aggiungi";
        $productEdit = null;
        $productRand = null;
        $double = false;
        $qrMessage = null;

        $em = $this->app->entityManagerFactory->create('ProductBrand');
        $brands = $em->findAll(null, 'order by `name`');

        $em = $this->app->entityManagerFactory->create('Lang');
        $langs = $em->findAll();

        $em = $this->app->entityManagerFactory->create('ProductSeason');
        $seasons = $em->findAll();

        $em = $this->app->entityManagerFactory->create('ProductSizeGroup');
        $sizesGroups = $em->findAll(null, 'order by locale, macroName, `name`');

        $em = $this->app->entityManagerFactory->create('Shop');
        $shops = $em->findAll(null, 'order by `name`');

        $em = $this->app->entityManagerFactory->create('Tag');
        $tag = $em->findAll(null, 'order by `slug`');

        $em = $this->app->entityManagerFactory->create('ProductColorGroup');
        $gruppicolore = $em->findBySql("SELECT * FROM ProductColorGroup WHERE langId = 1 ORDER BY `name`", array());

        $em = $this->app->entityManagerFactory->create('ProductSheetPrototype');
        $productSheets = $em->query('SELECT id, name FROM ProductSheetPrototype ORDER BY `name`')->fetchAll();

        $em = $this->app->entityManagerFactory->create('ProductStatus');
        $productStatuses = $em->findAll();

        $productDetailsCollection = $this->app->repoFactory->create('ProductDetailTranslation')->findBy(['langId'=>1]);
        $productDetails = [];
        foreach ($productDetailsCollection as $detail) {
            try {
                $productDetails[$detail->productDetailId] = $detail->name;
            } catch(\Throwable $e) {

            }
        }
        
        $statuses = [];
        $statuses['selected'] = 'S';
        foreach($productStatuses as $status){
            $statuses[$status->code] = $status->name;
        }

	    return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'statuses' =>$statuses,
            'tags' =>$tag,
            'dummyUrl' => $dummyUrl,
            'fileFolder' =>$fileFolder,
            'elenco' =>$elenco,
            'shops'=>$shops,
            'productRand'=> $productRand,
            'nuovoprodotto' =>$nuovoprodotto,
            'qrMessage' => $qrMessage,
            'productEdit' => $productEdit,
            'brands' => $brands,
            'langs' => $langs,
            'seasons' => $seasons,
            'bluesealBase' => $bluesealBase,
            'double'=>$double,
            'sizesGroups' => $sizesGroups,
            'gruppicolore' => $gruppicolore,
            'productSheets' => $productSheets,
            'page' => $this->page,
            'sidebar' => $this->sidebar->build(),
            'productDetails' => $productDetails
        ]);
    }
}