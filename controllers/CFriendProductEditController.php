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
class CFriendProductEditController extends CProductManageController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "smart_product_edit";

    /**
     * @throws \Exception
     * @throws \bamboo\core\exceptions\RedPandaDBALException
     * @throws \bamboo\core\exceptions\RedPandaORMException
     */

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/friend_product_edit.php');

        /** @var CUserRepo */
        $user = $this->app->getUser();
        $get = $this->app->router->request()->getRequestData();

        $product = $this->app->repoFactory->create('Product');



        $em = $this->app->entityManagerFactory->create('ProductBrand');
        $brands = $em->findAll(null, 'order by `name`');

        $em = $this->app->entityManagerFactory->create('Lang');
        $langs = $em->findAll();

        $em = $this->app->entityManagerFactory->create('ProductSeason');
        $seasons = $em->findAll();

        $em = $this->app->entityManagerFactory->create('ProductSizeGroup');
        $sizesGroups = $em->findAll(null, 'order by locale, macroName, `name`');

        $em = $this->app->entityManagerFactory->create('Shop');

        $allShops = false;
        if ($user->hasPermission('allShops')) $allShops = true;

        $em = $this->app->entityManagerFactory->create('Tag');
        $tag = $em->findAll(null, 'order by `slug`');

        $em = $this->app->entityManagerFactory->create('ProductColorGroup');
        $gruppicolore = $em->findBySql("SELECT * FROM ProductColorGroup WHERE langId = 1 ORDER BY `name`", []);

        $em = $this->app->entityManagerFactory->create('ProductSheetPrototype');
        $productSheets = $em->findBySql('SELECT id FROM ProductSheetPrototype ORDER BY `name`');

        $em = $this->app->entityManagerFactory->create('ProductStatus');
        $productStatuses = $em->findAll();

        foreach($productStatuses as $status) {
            $statuses[$status->id] = $status->name;
        }

        /* $em = $this->app->entityManagerFactory->create('Product', false);
         $productEdit = $em->findOne(array($_GET['id'], $_GET['productVariantId']));

         $cats = [];

         foreach($productEdit->productCategory as $v) {
             $path = $this->app->categoryManager->categories()->getPath($v->id);
             unset($path[0]);
             $cats[] = '<span>'.implode('/',array_column($path, 'slug')).'</span>';
         $statuses = [];
             $statuses['selected'] = $productEdit->productStatusId;
         }
         }*/

        $em = $this->app->entityManagerFactory->create('SortingPriority');
        $sortingPriorities = $em->findAll();

        $sortingOptions = [];
        foreach($sortingPriorities as $sortingPriority){
            $sortingOptions[$sortingPriority->id] = $sortingPriority->priority;
        }

        /* LETTURA GET PER PREPARARE MODIFICA
        if (!isset($_GET) || !isset($_GET['id']) || !isset($_GET['productVariantId'])) {
            throw new \Exception('You are not editing anything');
        }

        /** LOGICa
        $bluesealBase = $this->app->baseUrl(false) . '/blueseal/';
        $fileFolder = $this->app->rootPath().$this->app->cfg()->fetch('paths', 'dummyFolder') . '/';
        $dummyUrl = $this->app->cfg()->fetch('paths', 'dummyUrl') . '/';
        $elenco = $bluesealBase . "prodotti";
        $nuovoprodotto = $bluesealBase . "prodotti/modifica";
        $productEdit = null;
        $productRand = null;
        $qrMessage = null;




        //$this->app->vendorLibraries->load("aztec");
        $qrMessage = $productEdit->getAztecCode();
        $qrMessage = base64_encode($qrMessage);
        */
        $productDetailsCollection = $this->app->repoFactory->create('ProductDetailTranslation')->findBy(['langId'=>1]);
        $productDetails = [];

        foreach ($productDetailsCollection as $detail) {
            try {
                $productDetails[$detail->productDetailId] = $detail->name;
            } catch(\Exception $e) {

            }
        }
        unset($productDetailsCollection);

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'sidebar' => $this->sidebar->build(),
            'allShops' => $allShops,
            'statuses' => $statuses,
            'brands' => $brands,
            'double' => false,
            'langs' => $langs,
            'seasons' => $seasons,
            'sizesGroups' => $sizesGroups,
            'gruppicolore' => $gruppicolore,
            'productSheets' => $productSheets,
            'tags' => $tag,
            'sortingOptions' => $sortingOptions,
            'productDetails' => $productDetails
        ]);
        /*
        'dummyUrl' => $dummyUrl,
        'fileFolder' => $fileFolder,
        'elenco' => $elenco,
        'productRand' => $productRand,
        'nuovoprodotto' => $nuovoprodotto,
        'qrMessage' => $qrMessage,

        'categories' => $cats,
        'productDetails' => $productDetails*/
    }
}