<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\nestedCategory\CCategoryManager;
use bamboo\ecommerce\views\VBase;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class CProductEditController
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
class CProductEditController extends CProductManageController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "product_edit";

    /**
     * @throws \Exception
     * @throws \bamboo\core\exceptions\RedPandaDBALException
     * @throws \bamboo\core\exceptions\RedPandaORMException
     */
    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/product_edit.php');

	    /** LETTURA GET PER PREPARARE MODIFICA */
	    if (!isset($_GET) || !isset($_GET['id']) || !isset($_GET['productVariantId'])) {
		    throw new \Exception('You are not editing anything');
	    }

		/** LOGICA */
		$bluesealBase = $this->app->baseUrl(false) . '/blueseal/';
		$fileFolder = $this->app->rootPath().$this->app->cfg()->fetch('paths', 'dummyFolder') . '/';
		$dummyUrl = $this->app->cfg()->fetch('paths', 'dummyUrl') . '/';
		$elenco = $bluesealBase . "prodotti";
		$nuovoprodotto = $bluesealBase . "prodotti/modifica";
		$productEdit = null;
		$productRand = null;
		$qrMessage = null;

		$em = $this->app->entityManagerFactory->create('ProductBrand');
		$brands = $em->findAll(null, 'order by `name`');

		$em = $this->app->entityManagerFactory->create('Lang');
		$langs = $em->findAll();

		$em = $this->app->entityManagerFactory->create('ProductSeason');
		$seasons = $em->findAll();

		$em = $this->app->entityManagerFactory->create('ProductSizeGroup');
		$sizesGroups = $em->findAll(null, 'order by locale, `name`');

		$em = $this->app->entityManagerFactory->create('Shop');
		$shops = $em->findAll(null, 'order by `name`');

		$em = $this->app->entityManagerFactory->create('Tag');
		$tag = $em->findAll(null, 'order by `slug`');

		$em = $this->app->entityManagerFactory->create('ProductColorGroup');
		$gruppicolore = $em->findBySql("SELECT * FROM ProductColorGroup ORDER BY `name`", []);

	    $em = $this->app->entityManagerFactory->create('Product', false);
	    $productEdit = $em->findOne(array($_GET['id'], $_GET['productVariantId']));

        $cats = [];

        foreach($productEdit->productCategory as $v) {
            $path = $this->app->categoryManager->categories()->getPath($v->id);
            unset($path[0]);
            $cats[] = '<span>'.implode('/',array_column($path, 'slug')).'</span>';
        }


	    //$this->app->vendorLibraries->load("aztec");
	    $qrMessage = $productEdit->getAztecCode();
	    $qrMessage = base64_encode($qrMessage);

	    $em = $this->app->entityManagerFactory->create('ProductStatus');
        $productStatuses = $em->findAll();

	    $statuses = [];
	    $statuses['selected'] = $productEdit->productStatusId;
	    foreach($productStatuses as $status){
		    $statuses[$status->id] = $status->name;
	    }

	    $em = $this->app->entityManagerFactory->create('SortingPriority');
        $sortingPriorities = $em->findAll();

	    $sortingOptions = [];
	    foreach($sortingPriorities as $sortingPriority){
		    $sortingOptions[$sortingPriority->id] = $sortingPriority->priority;
	    }

	    $productDetailsCollection = \Monkey::app()->repoFactory->create('ProductDetailTranslation')->findBy(['langId'=>1]);
	    $productDetails = [];

	    foreach ($productDetailsCollection as $detail) {
		    try {
			    $productDetails[$detail->productDetailId] = $detail->name;
		    } catch(\Throwable $e) {

		    }
	    }
	    unset($productDetailsCollection);

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'statuses' => $statuses,
            'sortingOptions' => $sortingOptions,
            'tags' => $tag,
            'dummyUrl' => $dummyUrl,
            'fileFolder' => $fileFolder,
            'elenco' => $elenco,
            'shops' => $shops,
            'productRand' => $productRand,
            'nuovoprodotto' => $nuovoprodotto,
            'qrMessage' => $qrMessage,
            'productEdit' => $productEdit,
            'brands' => $brands,
            'double' => false,
            'langs' => $langs,
            'seasons' => $seasons,
            'sizesGroups' => $sizesGroups,
            'gruppicolore' => $gruppicolore,
            'page' => $this->page,
            'sidebar' => $this->sidebar->build(),
            'categories' => $cats,
	        'productDetails' => $productDetails
        ]);
    }
}