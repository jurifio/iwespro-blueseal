<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\domain\entities\CProductCategory;
use bamboo\domain\entities\CProductCategoryTranslation;
use bamboo\core\intl\CLang;

/**
 * Class CCategoryTranslationListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 07/06/2021
 * @since 1.0
 */
class CCategoryTranslationListAjaxController extends AAjaxController
{
    /**
     * @return string
     */
    public function get()
    {
        $currentUser = $this->app->getUser()->getId();
        if ($this->app->getUser()->hasPermission('allShops')) {
            $sql = "SELECT pct.productCategoryId as productCategoryId,
                        `pct`.`name` as `name`,
                        pc.slug as slug,
                        if(pct.description!='','sisì','no') as hasDescription,
                        pct.description as description, 
                        if(pct.longDescription!='','sisì','no') as hasLongDescription,
                        pc.depth as depth,
                        pct.langId as langId,
                        pct.shopId as shopId,   
                        pc.id as categoryId,
                        s.`name` as shopName,
                        l.`name` as langName
                from ProductCategoryTranslation pct  JOIN 
                ProductCategory pc on pct.productCategoryId=pc.id
join Lang l on  pct.langId=l.id 
join Shop s on pct.shopId=s.id
                GROUP BY pc.id order by pc.id";
        } else {
            $userHasShop = \Monkey::app()->repoFactory->create('UserHasShop')->findOneBy(['userId' => $currentUser]);
            $sql = "SELECT pct.productCategoryId as productCategoryId,
                        `pct`.`name` as `name`,
                        pc.slug as slug,
                        if(pct.description!='','sisì','no') as hasDescription,
                        pct.description as description, 
                        if(pct.longDescription!='','sisì','no') as hasLongDescription,
                        pc.depth as depth,
                        pct.langId as langId,
                        pct.shopId as shopId,    
                        pc.id as categoryId,
                        s.`name` as shopName,
                        l.`name` as langName
                from ProductCategoryTranslation pct  JOIN 
                ProductCategory pc on pct.productCategoryId=pc.id
join Lang l on  pct.langId=l.id
join Shop s on pct.shopId=s.id where pct.shopId=" . $userHasShop->shopId . "
                GROUP BY pc.id order by pc.id";
        }

        $datatable = new CDataTables($sql,['productCategoryId','langId','shopId'],$_GET);

        $category = \Monkey::app()->repoFactory->create('ProductCategoryTranslation')->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = \Monkey::app()->repoFactory->create('ProductCategoryTranslation')->em()->findCountBySql($datatable->getQuery(true),$datatable->getParams());
        $totalCount = \Monkey::app()->repoFactory->create('ProductCategoryTranslation')->em()->findCountBySql($datatable->getQuery('full'),$datatable->getParams());
        $pcRepo = \Monkey::app()->repoFactory->create('ProductCategory');
        $langRepo = \Monkey::app()->repoFactory->create('Lang');
        $modifica = $this->app->baseUrl(false) . "/blueseal/prodotti/categorie/traduzioni/modifica";
        $shopRepo = \Monkey::app()->repoFactory->create('Shop');
        $okManage = $this->app->getUser()->hasPermission('/admin/product/edit');

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $i = 0;

        foreach ($category as $val) {

            $response['data'][$i]["DT_RowId"] = $val->productCategoryId;
            /** @var  $pc CProductCategory */
            $pc = $pcRepo->findOneBy(['id' => $val->productCategoryId]);
            $response['data'][$i]['name'] = $okManage ? '<a data-toggle="tooltip" title="modifica" data-placement="right" href="' . $modifica . '?id=' . $val->productCategoryId . '">' . $val->name . '</a>' : $val->name;
            $response['data'][$i]['nameCat'] = $val->name;
            $response['data'][$i]['slug'] = $pc->slug;
            $cats = \Monkey::app()->categoryManager->categories()->getPath($val->productCategoryId);
            $type = [];
            $y = 0;
            foreach ($cats as $cat) {
                if ($cat['id'] == 1) continue;
                $type[] = \Monkey::app()->repoFactory->create('ProductCategory')->findOne([$cat['id']])->getLocalizedName();
                $y++;
            }

            //$categories[] = implode('/',$type);
            $response['data'][$i]['categoryId'] = '<span class="small">' . implode('/',$type) . '</span>';

            $lang = $langRepo->findOneBy(['id' => $val->langId]);
            $response['data'][$i]['langName'] = $lang->name;
            $response['data'][$i]['langId'] = $lang->id;
            $shop = $shopRepo->findOneBy(['id' => $val->shopId]);
            $response['data'][$i]['shopId'] = $shop->id;
            $response['data'][$i]['shopName'] = $shop->name;
            $response['data'][$i]['hasDescription'] = ($val->description != '') ? 'Si' : 'No';
            $response['data'][$i]['hasLongDescription'] = ($val->longDescription != '') ? 'Si' : 'No';

            $i++;
        }

        return json_encode($response);
    }
}