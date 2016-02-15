<?php
namespace bamboo\blueseal\controllers

use bamboo\domain\entities\CShop;
use bamboo\ecommerce\views\VBase;
use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\entities\CEntityManager;
use bamboo\core\db\pandaorm\adapter\CMySQLAdapter;
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
        $view->setTemplatePath($this->app->cfg()->fetch('paths','blueseal').'/template/product_add.php');

        /** LOGICA */
        $bluesealBase = $this->app->baseUrl(false).'/blueseal/';
        $fileFolder = $this->app->cfg()->fetch('paths','dummyFolder').'/';
        $dummyUrl =  $this->app->cfg()->fetch('paths','dummyUrl').'/';
        $elenco = $bluesealBase."prodotti";
        $nuovoprodotto = $bluesealBase."prodotti/aggiungi";
        $productEdit = null;
        $productRand = null;
        $double = false;
        $qrMessage = null;

        $em = $this->app->entityManagerFactory->create('ProductBrand');
        $brands = $em->findAll('limit 9999','order by `name`');

        $em = $this->app->entityManagerFactory->create('Lang');
        $langs = $em->findAll('limit 9999','');

        $em = $this->app->entityManagerFactory->create('ProductSeason');
        $seasons = $em->findAll('limit 9999','');

        $em = $this->app->entityManagerFactory->create('ProductSizeGroup');
        $sizesGroups = $em->findAll('limit 9999','order by locale, macroName, `name`');

        $em = $this->app->entityManagerFactory->create('Shop');
        $shops = $em->findAll('limit 9999','order by `name`');

        $em = $this->app->entityManagerFactory->create('Tag');
        $tag = $em->findAll('limit 9999','order by `slug`');

        $em = $this->app->entityManagerFactory->create('ProductColorGroup');
        $gruppicolore = $em->findBySql("select * from ProductColorGroup where langId = 1 order by `name`",array());

        $em = $this->app->entityManagerFactory->create('ProductSheet');
        $productSheets = $em->query('SELECT distinct `name` FROM ProductSheet order by `name`')->fetchAll();

        $em = $this->app->entityManagerFactory->create('ProductStatus');
        $productStatuses = $em->findAll('limit 99','');

        $statuses = [];
        $statuses['selected'] = 'S';
        foreach($productStatuses as $status){
            $statuses[$status->code] = $status->name;
        }

        $detailsGroups = array();
        if(isset($productEdit) && isset($productEdit->sheetName)){
            $em = $this->app->entityManagerFactory->create('ProductAttribute');
            foreach($langs as $lang){
                $sql = 'SELECT productAttributeId as id FROM ProductSheet where `name` = "'.$productEdit->sheetName.'"  ';
                $detailsGroups[$lang->lang] = $em->findBySql($sql);
            }
        }

        echo $view->render([
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
            'detailsGroups' => $detailsGroups,
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}