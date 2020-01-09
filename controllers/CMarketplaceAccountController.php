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
class CMarketplaceAccountController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "marketplace-account_edit";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath() . $this->app->cfg()->fetch('paths','blueseal') . '/template/marketplace_account_edit.php');
        $marketplaceAccountGet = \Monkey::app()->router->request()->getRequestData('id');
        $marketplaceCode = explode('-',$marketplaceAccountGet);
        $productSizeGroupRepo = \Monkey::app()->repoFactory->create('ProductSizeGroup');


        $marketplaceAccount = \Monkey::app()->repoFactory->create('MarketplaceAccount')->findOneBy(['id' => $marketplaceCode[0],'marketplaceId' => $marketplaceCode[1]]);

        $marketplaceConfig = json_encode($marketplaceAccount->config,false);
        $countConfig = json_decode($marketplaceConfig,true);
        $countField = count($countConfig);

        $productCategoryEx1Option = '';
        $productCategoryEx2Option = '';
        $productCategoryEx3Option = '';
        $productCategoryEx4Option = '';
        $productCategoryEx5Option = '';
        $productCategoryEx6Option = '';
        $productCategoryIdEx1 = $marketplaceAccount->config['productCategoryIdEx1'];

        $sqlCategory = 'SELECT t0.id as  id,t0.slug as slug
      ,(SELECT GROUP_CONCAT(t2.slug)
                    FROM ProductCategory t2
                    WHERE t2.lft<t0.lft AND t2.rght>t0.rght
                    ORDER BY t2.lft) ancestors
FROM ProductCategory  t0   GROUP BY t0.slug';
        $res_category = \Monkey::app()->dbAdapter->query($sqlCategory,[])->fetchAll();

        foreach ($res_category as $category) {
            $productCategoryIdEx1Text = str_replace(',','/',($category['ancestors'] . ',' . $category['slug']));
            if ($category['id'] == $productCategoryIdEx1) {
                $productCategoryEx1Option .= '<option selected="selected" value ="' . $category['id'] . '">' . $productCategoryIdEx1Text . '</option>';
            } else {
                $productCategoryEx1Option .= '<option  value ="' . $category['id'] . '">' . $productCategoryIdEx1Text . '</option>';
            }
        }
        $productCategoryIdEx2 = $marketplaceAccount->config['productCategoryIdEx2'];
        $sqlCategory = 'SELECT t0.id as  id,t0.slug as slug
      ,(SELECT GROUP_CONCAT(t2.slug)
                    FROM ProductCategory t2
                    WHERE t2.lft<t0.lft AND t2.rght>t0.rght
                    ORDER BY t2.lft) ancestors
FROM ProductCategory  t0   GROUP BY t0.slug';
        $res_category = \Monkey::app()->dbAdapter->query($sqlCategory,[])->fetchAll();

        foreach ($res_category as $category) {
            $productCategoryIdEx2Text = str_replace(',','/',($category['ancestors'] . ',' . $category['slug']));
            if ($category['id'] == $productCategoryIdEx2) {
                $productCategoryEx2Option .= '<option selected="selected" value ="' . $category['id'] . '">' . $productCategoryIdEx2Text . '</option>';
            } else {
                $productCategoryEx2Option .= '<option  value ="' . $category['id'] . '">' . $productCategoryIdEx2Text . '</option>';
            }
        }
        $productCategoryIdEx3 = $marketplaceAccount->config['productCategoryIdEx3'];
        $sqlCategory = 'SELECT t0.id as  id,t0.slug as slug
      ,(SELECT GROUP_CONCAT(t2.slug)
                    FROM ProductCategory t2
                    WHERE t2.lft<t0.lft AND t2.rght>t0.rght
                    ORDER BY t2.lft) ancestors
FROM ProductCategory  t0   GROUP BY t0.slug';
        $res_category = \Monkey::app()->dbAdapter->query($sqlCategory,[])->fetchAll();

        foreach ($res_category as $category) {
            $productCategoryIdEx3Text = str_replace(',','/',($category['ancestors'] . ',' . $category['slug']));
            if ($category['id'] == $productCategoryIdEx3) {
                $productCategoryEx3Option .= '<option selected="selected" value ="' . $category['id'] . '">' . $productCategoryIdEx3Text . '</option>';
            } else {
                $productCategoryEx3Option .= '<option  value ="' . $category['id'] . '">' . $productCategoryIdEx3Text . '</option>';
            }
        }
        $productCategoryIdEx4 = $marketplaceAccount->config['productCategoryIdEx4'];
        $sqlCategory = 'SELECT t0.id as  id,t0.slug as slug
      ,(SELECT GROUP_CONCAT(t2.slug)
                    FROM ProductCategory t2
                    WHERE t2.lft<t0.lft AND t2.rght>t0.rght
                    ORDER BY t2.lft) ancestors
FROM ProductCategory  t0   GROUP BY t0.slug';
        $res_category = \Monkey::app()->dbAdapter->query($sqlCategory,[])->fetchAll();

        foreach ($res_category as $category) {
            $productCategoryIdEx4Text = str_replace(',','/',($category['ancestors'] . ',' . $category['slug']));
            if ($category['id'] == $productCategoryIdEx4) {
                $productCategoryEx4Option .= '<option selected="selected" value ="' . $category['id'] . '">' . $productCategoryIdEx4Text . '</option>';
            } else {
                $productCategoryEx4Option .= '<option  value ="' . $category['id'] . '">' . $productCategoryIdEx4Text . '</option>';
            }
        }
        $productCategoryIdEx5 = $marketplaceAccount->config['productCategoryIdEx5'];
        $sqlCategory = 'SELECT t0.id as  id,t0.slug as slug
      ,(SELECT GROUP_CONCAT(t2.slug)
                    FROM ProductCategory t2
                    WHERE t2.lft<t0.lft AND t2.rght>t0.rght
                    ORDER BY t2.lft) ancestors
FROM ProductCategory  t0   GROUP BY t0.slug';
        $res_category = \Monkey::app()->dbAdapter->query($sqlCategory,[])->fetchAll();

        foreach ($res_category as $category) {
            $productCategoryIdEx5Text = str_replace(',','/',($category['ancestors'] . ',' . $category['slug']));
            if ($category['id'] == $productCategoryIdEx5) {
                $productCategoryEx5Option .= '<option selected="selected" value ="' . $category['id'] . '">' . $productCategoryIdEx5Text . '</option>';
            } else {
                $productCategoryEx5Option .= '<option  value ="' . $category['id'] . '">' . $productCategoryIdEx5Text . '</option>';
            }
        }
        $productCategoryIdEx6 = $marketplaceAccount->config['productCategoryIdEx1'];
        $sqlCategory = 'SELECT t0.id as  id,t0.slug as slug
      ,(SELECT GROUP_CONCAT(t2.slug)
                    FROM ProductCategory t2
                    WHERE t2.lft<t0.lft AND t2.rght>t0.rght
                    ORDER BY t2.lft) ancestors
FROM ProductCategory  t0   GROUP BY t0.slug';
        $res_category = \Monkey::app()->dbAdapter->query($sqlCategory,[])->fetchAll();

        foreach ($res_category as $category) {
            $productCategoryIdEx6Text = str_replace(',','/',($category['ancestors'] . ',' . $category['slug']));
            if ($category['id'] == $productCategoryIdEx6) {
                $productCategoryEx6Option .= '<option selected="selected" value ="' . $category['id'] . '">' . $productCategoryIdEx6Text . '</option>';
            } else {
                $productCategoryEx6Option .= '<option  value ="' . $category['id'] . '">' . $productCategoryIdEx6Text . '</option>';
            }
        }
        $productSizeGroupEx1 = $marketplaceAccount->config['productSizeGroupEx1'];
        $productSizeGroup = $productSizeGroupRepo->findOneBy(['id' => $productSizeGroupEx1]);
        $productSizeGroupEx1Text = $productSizeGroup->name;
        $productSizeGroupEx2 = $marketplaceAccount->config['productSizeGroupEx2'];
        $productSizeGroup = $productSizeGroupRepo->findOneBy(['id' => $productSizeGroupEx2]);
        $productSizeGroupEx2Text = $productSizeGroup->name;
        $productSizeGroupEx3 = $marketplaceAccount->config['productSizeGroupEx3'];
        $productSizeGroup = $productSizeGroupRepo->findOneBy(['id' => $productSizeGroupEx3]);
        $productSizeGroupEx3Text = $productSizeGroup->name;
        $productSizeGroupEx4 = $marketplaceAccount->config['productSizeGroupEx4'];
        $productSizeGroup = $productSizeGroupRepo->findOneBy(['id' => $productSizeGroupEx4]);
        $productSizeGroupEx4Text = $productSizeGroup->name;
        $productSizeGroupEx5 = $marketplaceAccount->config['productSizeGroupEx5'];
        $productSizeGroup = $productSizeGroupRepo->findOneBy(['id' => $productSizeGroupEx5]);
        $productSizeGroupEx5Text = $productSizeGroup->name;
        $productSizeGroupEx6 = $marketplaceAccount->config['productSizeGroupEx6'];
        $productSizeGroup = $productSizeGroupRepo->findOneBy(['id' => $productSizeGroupEx6]);
        $productSizeGroupEx6Text = $productSizeGroup->name;
        $productCategory1Option = '';
        $productCategory2Option = '';
        $productCategory3Option = '';
        $productCategory4Option = '';
        $productCategory5Option = '';



        $productCategoryId1 = $marketplaceAccount->config['productCategoryId1'];

        $sqlCategory = 'SELECT t0.id as  id,t0.slug as slug
      ,(SELECT GROUP_CONCAT(t2.slug)
                    FROM ProductCategory t2
                    WHERE t2.lft<t0.lft AND t2.rght>t0.rght
                    ORDER BY t2.lft) ancestors
FROM ProductCategory  t0   GROUP BY t0.slug';
        $res_category = \Monkey::app()->dbAdapter->query($sqlCategory,[])->fetchAll();
        foreach ($res_category as $category) {
            $productCategoryId1Text = str_replace(',','/',($category['ancestors'] . ',' . $category['slug']));
            if ($category['id'] == $productCategoryId1) {
                $productCategory1Option .= '<option selected="selected" value ="' . $category['id'] . '">' . $productCategoryId1Text . '</option>';
            } else {
                $productCategory1Option .= '<option  value ="' . $category['id'] . '">' . $productCategoryId1Text . '</option>';
            }
        }
        $productCategoryId2 = $marketplaceAccount->config['productCategoryId2'];

        $sqlCategory = 'SELECT t0.id as  id,t0.slug as slug
      ,(SELECT GROUP_CONCAT(t2.slug)
                    FROM ProductCategory t2
                    WHERE t2.lft<t0.lft AND t2.rght>t0.rght
                    ORDER BY t2.lft) ancestors
FROM ProductCategory  t0   GROUP BY t0.slug';
        $res_category = \Monkey::app()->dbAdapter->query($sqlCategory,[])->fetchAll();

        foreach ($res_category as $category) {
            $productCategoryId2Text = str_replace(',','/',($category['ancestors'] . ',' . $category['slug']));
            if ($category['id'] == $productCategoryId2) {
                $productCategory2Option .= '<option selected="selected" value ="' . $category['id'] . '">' . $productCategoryId2Text . '</option>';
            } else {
                $productCategory2Option .= '<option  value ="' . $category['id'] . '">' . $productCategoryId2Text . '</option>';
            }
        }

        $productCategoryId3 = $marketplaceAccount->config['productCategoryId3'];

        $sqlCategory = 'SELECT t0.id as  id,t0.slug as slug
      ,(SELECT GROUP_CONCAT(t2.slug)
                    FROM ProductCategory t2
                    WHERE t2.lft<t0.lft AND t2.rght>t0.rght
                    ORDER BY t2.lft) ancestors
FROM ProductCategory  t0   GROUP BY t0.slug';
        $res_category = \Monkey::app()->dbAdapter->query($sqlCategory,[])->fetchAll();

        foreach ($res_category as $category) {
            $productCategoryId3Text = str_replace(',','/',($category['ancestors'] . ',' . $category['slug']));
            if ($category['id'] == $productCategoryId3) {
                $productCategory3Option .= '<option selected="selected" value ="' . $category['id'] . '">' . $productCategoryId3Text . '</option>';
            } else {
                $productCategory3Option .= '<option  value ="' . $category['id'] . '">' . $productCategoryId3Text . '</option>';
            }
        }
        $productCategoryId4 = $marketplaceAccount->config['productCategoryId4'];

        $sqlCategory = 'SELECT t0.id as  id,t0.slug as slug
      ,(SELECT GROUP_CONCAT(t2.slug)
                    FROM ProductCategory t2
                    WHERE t2.lft<t0.lft AND t2.rght>t0.rght
                    ORDER BY t2.lft) ancestors
FROM ProductCategory  t0   GROUP BY t0.slug';
        $res_category = \Monkey::app()->dbAdapter->query($sqlCategory,[])->fetchAll();

        foreach ($res_category as $category) {
            $productCategoryId4Text = str_replace(',','/',($category['ancestors'] . ',' . $category['slug']));
            if ($category['id'] == $productCategoryId4) {
                $productCategory4Option .= '<option selected="selected" value ="' . $category['id'] . '">' . $productCategoryId4Text . '</option>';
            } else {
                $productCategory4Option .= '<option  value ="' . $category['id'] . '">' . $productCategoryId4Text . '</option>';
            }
        }
        $productCategoryId5 = $marketplaceAccount->config['productCategoryId5'];

        $sqlCategory = 'SELECT t0.id as  id,t0.slug as slug
      ,(SELECT GROUP_CONCAT(t2.slug)
                    FROM ProductCategory t2
                    WHERE t2.lft<t0.lft AND t2.rght>t0.rght
                    ORDER BY t2.lft) ancestors
FROM ProductCategory  t0   GROUP BY t0.slug';
        $res_category = \Monkey::app()->dbAdapter->query($sqlCategory,[])->fetchAll();

        foreach ($res_category as $category) {
            $productCategoryId5Text = str_replace(',','/',($category['ancestors'] . ',' . $category['slug']));
            if ($category['id'] == $productCategoryId5) {
                $productCategory5Option .= '<option selected="selected" value ="' . $category['id'] . '">' . $productCategoryId5Text . '</option>';
            } else {
                $productCategory5Option .= '<option  value ="' . $category['id'] . '">' . $productCategoryId5Text . '</option>';
            }
        }


        $productSizeGroupId1 = $marketplaceAccount->config['productSizeGroup1'];
        $productSizeGroup = $productSizeGroupRepo->findOneBy(['id' => $productSizeGroupId1]);
        $productSizeGroupId1Text = $productSizeGroup->name;
        $productSizeGroupId2 = $marketplaceAccount->config['productSizeGroup2'];
        $productSizeGroup = $productSizeGroupRepo->findOneBy(['id' => $productSizeGroupId2]);
        $productSizeGroupId2Text = $productSizeGroup->name;
        $productSizeGroupId3 = $marketplaceAccount->config['productSizeGroup3'];
        $productSizeGroup = $productSizeGroupRepo->findOneBy(['id' => $productSizeGroupId3]);
        $productSizeGroupId3Text = $productSizeGroup->name;
        $productSizeGroupId4 = $marketplaceAccount->config['productSizeGroup4'];
        $productSizeGroup = $productSizeGroupRepo->findOneBy(['id' => $productSizeGroupId4]);
        $productSizeGroupId4Text = $productSizeGroup->name;
        $productSizeGroupId5 = $marketplaceAccount->config['productSizeGroup5'];
        $productSizeGroup = $productSizeGroupRepo->findOneBy(['id' => $productSizeGroupId5]);
        $productSizeGroupId5Text = $productSizeGroup->name;


        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'marketplaceAccountGet' => $marketplaceAccountGet,
            'marketplaceAccount' => $marketplaceAccount,
            'marketplaceConfig' => $marketplaceConfig,
            'marketplaceCode' => $marketplaceCode,
            'countField' => $countField,
            'productSizeGroupEx1' => $productSizeGroupEx1,
            'productSizeGroupEx2' => $productSizeGroupEx2,
            'productSizeGroupEx3' => $productSizeGroupEx3,
            'productSizeGroupEx4' => $productSizeGroupEx4,
            'productSizeGroupEx5' => $productSizeGroupEx5,
            'productSizeGroupEx6' => $productSizeGroupEx6,
            'productSizeGroupEx1Text' => $productSizeGroupEx1Text,
            'productSizeGroupEx2Text' => $productSizeGroupEx2Text,
            'productSizeGroupEx3Text' => $productSizeGroupEx3Text,
            'productSizeGroupEx4Text' => $productSizeGroupEx4Text,
            'productSizeGroupEx5Text' => $productSizeGroupEx5Text,
            'productSizeGroupEx6Text' => $productSizeGroupEx6Text,
            'productSizeGroupId1' => $productSizeGroupId1,
            'productSizeGroupId2' => $productSizeGroupId2,
            'productSizeGroupId3' => $productSizeGroupId3,
            'productSizeGroupId4' => $productSizeGroupId4,
            'productSizeGroupId5' => $productSizeGroupId5,
            'productSizeGroupId1Text' => $productSizeGroupId1Text,
            'productSizeGroupId2Text' => $productSizeGroupId2Text,
            'productSizeGroupId3Text' => $productSizeGroupId3Text,
            'productSizeGroupId4Text' => $productSizeGroupId4Text,
            'productSizeGroupId5Text' => $productSizeGroupId5Text,
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