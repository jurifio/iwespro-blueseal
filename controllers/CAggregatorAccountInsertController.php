<?php

namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;
use bamboo\domain\entities\CProduct;


/**
 * Class CShopListController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CAggregatorAccountInsertController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "aggregator-account_add";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath() . $this->app->cfg()->fetch('paths','blueseal') . '/template/aggregator_account_add.php');

        $productSizeGroupRepo = \Monkey::app()->repoFactory->create('ProductSizeGroup');
        $productBrands=\Monkey::app()->repoFactory->create('ProductBrand');
        $shops=\Monkey::app()->repoFactory->create('Shops');



        $config='{ "nameAggregator":"Nome Aggregatore","lang":"lingua isocode","slug":"nome abbreviato","filePath":" /export/nomeAggregatoreBetterFeedTemp.linguaisocode.xml","feedUrl":"/services/feed/linguaisocode/slugomeabbreviato",
"activeAutomatic":"0","emailDepublish":"email@depubblicazione.prodotti","emailNotifyOffline":"email@notifica.pubblicazioneoffline" }';
        $marketplaceConfig = json_decode($config,true);
       $countField = count($marketplaceConfig);
        $nameAggregator=$marketplaceConfig['nameAggregator'];
        $productCategoryEx1Option = '';
        $productCategoryEx2Option = '';
        $productCategoryEx3Option = '';
        $productCategoryEx4Option = '';
        $productCategoryEx5Option = '';
        $productCategoryEx6Option = '';


        $sqlCategory = 'SELECT t0.id as  id,t0.slug as slug
      ,(SELECT GROUP_CONCAT(t2.slug)
                    FROM ProductCategory t2
                    WHERE t2.lft<t0.lft AND t2.rght>t0.rght
                    ORDER BY t2.lft) ancestors
FROM ProductCategory  t0   GROUP BY t0.slug';
        $res_category = \Monkey::app()->dbAdapter->query($sqlCategory,[])->fetchAll();

        foreach ($res_category as $category) {
            $productCategoryIdEx1Text = str_replace(',','/',($category['ancestors'] . ',' . $category['slug']));
            $productCategoryEx1Option .= '<option  value ="' . $category['id'] . '">' . $productCategoryIdEx1Text . '</option>';
        }
        $sqlCategory = 'SELECT t0.id as  id,t0.slug as slug
      ,(SELECT GROUP_CONCAT(t2.slug)
                    FROM ProductCategory t2
                    WHERE t2.lft<t0.lft AND t2.rght>t0.rght
                    ORDER BY t2.lft) ancestors
FROM ProductCategory  t0   GROUP BY t0.slug';
        $res_category = \Monkey::app()->dbAdapter->query($sqlCategory,[])->fetchAll();

        foreach ($res_category as $category) {
            $productCategoryIdEx2Text = str_replace(',','/',($category['ancestors'] . ',' . $category['slug']));
                $productCategoryEx2Option .= '<option  value ="' . $category['id'] . '">' . $productCategoryIdEx2Text . '</option>';
        }

        $sqlCategory = 'SELECT t0.id as  id,t0.slug as slug
      ,(SELECT GROUP_CONCAT(t2.slug)
                    FROM ProductCategory t2
                    WHERE t2.lft<t0.lft AND t2.rght>t0.rght
                    ORDER BY t2.lft) ancestors
FROM ProductCategory  t0   GROUP BY t0.slug';
        $res_category = \Monkey::app()->dbAdapter->query($sqlCategory,[])->fetchAll();

        foreach ($res_category as $category) {
            $productCategoryIdEx3Text = str_replace(',','/',($category['ancestors'] . ',' . $category['slug']));
                $productCategoryEx3Option .= '<option  value ="' . $category['id'] . '">' . $productCategoryIdEx3Text . '</option>';
        }
        $sqlCategory = 'SELECT t0.id as  id,t0.slug as slug
      ,(SELECT GROUP_CONCAT(t2.slug)
                    FROM ProductCategory t2
                    WHERE t2.lft<t0.lft AND t2.rght>t0.rght
                    ORDER BY t2.lft) ancestors
FROM ProductCategory  t0   GROUP BY t0.slug';
        $res_category = \Monkey::app()->dbAdapter->query($sqlCategory,[])->fetchAll();

        foreach ($res_category as $category) {
            $productCategoryIdEx4Text = str_replace(',','/',($category['ancestors'] . ',' . $category['slug']));
                $productCategoryEx4Option .= '<option  value ="' . $category['id'] . '">' . $productCategoryIdEx4Text . '</option>';
        }
        $sqlCategory = 'SELECT t0.id as  id,t0.slug as slug
      ,(SELECT GROUP_CONCAT(t2.slug)
                    FROM ProductCategory t2
                    WHERE t2.lft<t0.lft AND t2.rght>t0.rght
                    ORDER BY t2.lft) ancestors
FROM ProductCategory  t0   GROUP BY t0.slug';
        $res_category = \Monkey::app()->dbAdapter->query($sqlCategory,[])->fetchAll();

        foreach ($res_category as $category) {
            $productCategoryIdEx5Text = str_replace(',','/',($category['ancestors'] . ',' . $category['slug']));
                $productCategoryEx5Option .= '<option  value ="' . $category['id'] . '">' . $productCategoryIdEx5Text . '</option>';
        }
        $sqlCategory = 'SELECT t0.id as  id,t0.slug as slug
      ,(SELECT GROUP_CONCAT(t2.slug)
                    FROM ProductCategory t2
                    WHERE t2.lft<t0.lft AND t2.rght>t0.rght
                    ORDER BY t2.lft) ancestors
FROM ProductCategory  t0   GROUP BY t0.slug';
        $res_category = \Monkey::app()->dbAdapter->query($sqlCategory,[])->fetchAll();

        foreach ($res_category as $category) {
            $productCategoryIdEx6Text = str_replace(',','/',($category['ancestors'] . ',' . $category['slug']));
                $productCategoryEx6Option .= '<option  value ="' . $category['id'] . '">' . $productCategoryIdEx6Text . '</option>';
        }

        $productCategory1Option = '';
        $productCategory2Option = '';
        $productCategory3Option = '';
        $productCategory4Option = '';
        $productCategory5Option = '';





        $sqlCategory = 'SELECT t0.id as  id,t0.slug as slug
      ,(SELECT GROUP_CONCAT(t2.slug)
                    FROM ProductCategory t2
                    WHERE t2.lft<t0.lft AND t2.rght>t0.rght
                    ORDER BY t2.lft) ancestors
FROM ProductCategory  t0   GROUP BY t0.slug';
        $res_category = \Monkey::app()->dbAdapter->query($sqlCategory,[])->fetchAll();
        foreach ($res_category as $category) {
            $productCategoryId1Text = str_replace(',','/',($category['ancestors'] . ',' . $category['slug']));
                $productCategory1Option .= '<option  value ="' . $category['id'] . '">' . $productCategoryId1Text . '</option>';
        }

        $sqlCategory = 'SELECT t0.id as  id,t0.slug as slug
      ,(SELECT GROUP_CONCAT(t2.slug)
                    FROM ProductCategory t2
                    WHERE t2.lft<t0.lft AND t2.rght>t0.rght
                    ORDER BY t2.lft) ancestors
FROM ProductCategory  t0   GROUP BY t0.slug';
        $res_category = \Monkey::app()->dbAdapter->query($sqlCategory,[])->fetchAll();

        foreach ($res_category as $category) {
            $productCategoryId2Text = str_replace(',','/',($category['ancestors'] . ',' . $category['slug']));
                $productCategory2Option .= '<option  value ="' . $category['id'] . '">' . $productCategoryId2Text . '</option>';
        }


        $sqlCategory = 'SELECT t0.id as  id,t0.slug as slug
      ,(SELECT GROUP_CONCAT(t2.slug)
                    FROM ProductCategory t2
                    WHERE t2.lft<t0.lft AND t2.rght>t0.rght
                    ORDER BY t2.lft) ancestors
FROM ProductCategory  t0   GROUP BY t0.slug';
        $res_category = \Monkey::app()->dbAdapter->query($sqlCategory,[])->fetchAll();

        foreach ($res_category as $category) {
            $productCategoryId3Text = str_replace(',','/',($category['ancestors'] . ',' . $category['slug']));
                $productCategory3Option .= '<option  value ="' . $category['id'] . '">' . $productCategoryId3Text . '</option>';
        }

        $sqlCategory = 'SELECT t0.id as  id,t0.slug as slug
      ,(SELECT GROUP_CONCAT(t2.slug)
                    FROM ProductCategory t2
                    WHERE t2.lft<t0.lft AND t2.rght>t0.rght
                    ORDER BY t2.lft) ancestors
FROM ProductCategory  t0   GROUP BY t0.slug';
        $res_category = \Monkey::app()->dbAdapter->query($sqlCategory,[])->fetchAll();

        foreach ($res_category as $category) {
            $productCategoryId4Text = str_replace(',','/',($category['ancestors'] . ',' . $category['slug']));
                $productCategory4Option .= '<option  value ="' . $category['id'] . '">' . $productCategoryId4Text . '</option>';
        }

        $sqlCategory = 'SELECT t0.id as  id,t0.slug as slug
      ,(SELECT GROUP_CONCAT(t2.slug)
                    FROM ProductCategory t2
                    WHERE t2.lft<t0.lft AND t2.rght>t0.rght
                    ORDER BY t2.lft) ancestors
FROM ProductCategory  t0   GROUP BY t0.slug';
        $res_category = \Monkey::app()->dbAdapter->query($sqlCategory,[])->fetchAll();

        foreach ($res_category as $category) {
            $productCategoryId5Text = str_replace(',','/',($category['ancestors'] . ',' . $category['slug']));
                $productCategory5Option .= '<option  value ="' . $category['id'] . '">' . $productCategoryId5Text . '</option>';
        }




        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'productCategory1Option'=>$productCategory1Option,
            'productCategory2Option'=>$productCategory2Option,
            'productCategory3Option'=>$productCategory3Option,
            'productCategory4Option'=>$productCategory4Option,
            'productCategory5Option'=>$productCategory5Option,
            'productCategoryEx1Option'=>$productCategoryEx1Option,
            'productCategoryEx2Option'=>$productCategoryEx2Option,
            'productCategoryEx3Option'=>$productCategoryEx3Option,
            'productCategoryEx4Option'=>$productCategoryEx4Option,
            'productCategoryEx5Option'=>$productCategoryEx5Option,
            'productCategoryEx6Option'=>$productCategoryEx6Option,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}