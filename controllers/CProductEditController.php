<?php
namespace bamboo\blueseal\controllers;

use bamboo\domain\entities\CShop;
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

        /** LOGICA */
        $bluesealBase = $this->app->baseUrl(false) . '/blueseal/';
        $fileFolder = $this->app->cfg()->fetch('paths', 'dummyFolder') . '/';
        $dummyUrl = $this->app->cfg()->fetch('paths', 'dummyUrl') . '/';
        $elenco = $bluesealBase . "prodotti";
        $nuovoprodotto = $bluesealBase . "prodotti/aggiungi";
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
        $sizesGroups = $em->findAll(null, 'order by locale, macroName, `name`');

        $em = $this->app->entityManagerFactory->create('Shop');
        $shops = $em->findAll(null, 'order by `name`');

        $em = $this->app->entityManagerFactory->create('Tag');
        $tag = $em->findAll(null, 'order by `slug`');

        $em = $this->app->entityManagerFactory->create('ProductColorGroup');
        $gruppicolore = $em->findBySql("SELECT * FROM ProductColorGroup WHERE langId = 1 ORDER BY `name`", array());

        $em = $this->app->entityManagerFactory->create('ProductSheet');
        $productSheets = $em->query('SELECT DISTINCT `name` FROM ProductSheet ORDER BY `name`')->fetchAll();

        /** LETTURA GET PER PREPARARE MODIFICA */
        if (isset($_GET) && isset($_GET['id']) && isset($_GET['productVariantId'])) {
            $em = $this->app->entityManagerFactory->create('Product', false);
            $productEdit = $em->findOne(array($_GET['id'], $_GET['productVariantId']));
            $this->app->vendorLibraries->load("aztec");
            $qrMessage = $productEdit->id . '-' . $productEdit->productVariantId . '__' . $productEdit->productBrand->slug . ' - ' . $productEdit->itemno . ' - ' . $productEdit->productVariant->name;
            $qrMessage = base64_encode($qrMessage);

        }
        $em = $this->app->entityManagerFactory->create('ProductStatus');
        $productStatuses = $em->findAll();

        $statuses = [];
        $statuses['selected'] = $productEdit->status;
        foreach($productStatuses as $status){
            $statuses[$status->code] = $status->name;
        }

        $detailsGroups = array();
        if (isset($productEdit) && isset($productEdit->sheetName)) {
            $em = $this->app->entityManagerFactory->create('ProductAttribute');
            foreach ($langs as $lang) {
                $sql = 'SELECT productAttributeId AS id FROM ProductSheet WHERE `name` = "' . $productEdit->sheetName . '"  ';
                $detailsGroups[$lang->lang] = $em->findBySql($sql);
            }
        }

        echo $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'statuses' => $statuses,
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
            'productSheets' => $productSheets,
            'detailsGroups' => $detailsGroups,
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}