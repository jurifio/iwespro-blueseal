<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\exceptions\BambooException;
use bamboo\core\intl\CLang;


/**
 * Class CNameTranslateLangListAjaxController
 * @package redpanda\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CNameTranslateLangListAjaxController extends AAjaxController
{
    protected $urls = [];
    protected $authorizedShops = [];
    protected $em;

    /**
     * @param $action
     * @return mixed
     */
    public function createAction($action)
    {
        $this->app->setLang(new CLang(1,'it'));
        $this->urls['base'] = $this->app->baseUrl(false)."/blueseal/";
        $this->urls['page'] = $this->urls['base']."prodotti";
        $this->urls['dummy'] = $this->app->cfg()->fetch('paths','dummyUrl');

        $this->em = new \stdClass();
        $this->em->productsName = $this->app->entityManagerFactory->create('ProductNameTranslation');

        return $this->{$action}();
    }

    public function get()
    {
        $langId = $this->app->router->request()->getRequestData('lang');
        $sql =
"SELECT
  `pn`.`id` as `id`,
  `pnt`.`productId` as `productId`,
  `pnt`.`productVariantId` as `productVariantId`,
  `pn`.`name` as `name`,
  `pn`.`langId` as langId,
  group_concat(`translated`.`langId`) as `langIdTranslated`,
  `pc`.`id` as category,
  0 as count,
  count( DISTINCT p.productVariantId) as countProds
FROM (((((`ProductName` as `pn`
  JOIN `ProductNameTranslation` as `pnt` on `pn`.`name` = `pnt`.`name` AND `pn`.`langId` = `pnt`.`langId`)
  JOIN `Product` `p` ON (`pnt`.`productId` = `p`.`id` AND `pnt`.`productVariantId` = `p`.`productVariantId`))
  JOIN `ProductSku` as `ps` ON `ps`.`productId` = `p`.`id` AND `ps`.`productVariantId` = `p`.`productVariantId`)
  LEFT JOIN (`ProductHasProductCategory` `phpc`
    JOIN `ProductCategory` `pc` ON `phpc`.`productCategoryId` = `pc`.`id`)
    ON (`p`.`id` = `phpc`.`productId`) AND (`p`.`productVariantId` = `phpc`.`productVariantId`)
  JOIN `ProductStatus` ON `ProductStatus`.`id` = `p`.`productStatusId`)
  LEFT JOIN (SELECT translation, name, langId FROM ProductName) as translated on translated.name = pn.name )
WHERE `p`.`qty` > 0 AND `p`.`dummyPicture` NOT LIKE '%bs-dummy%'
      AND `p`.`productStatusId` in (5,6,11)
group by pn.id, `pc`.`id`";
        $datatable = new CDataTables($sql,['id'],$_GET, true);

        $okManage = $this->app->getUser()->hasPermission('/admin/product/edit');

        if (!empty($this->authorizedShops)) {
            $datatable->addCondition('shopId',$this->authorizedShops);
        }

        $datatable->addCondition('langId',[1]);
        $datatable->addCondition('name',[''],true);

        $mark = \Monkey::app()->router->request()->getRequestData('marks');
        if ('con' == $mark) {
            $datatable->addIgnobleCondition('name', '% !', false);
        } elseif ('senza' == $mark) {
            $datatable->addIgnobleCondition('name', '% !', true);
        }

        $translated = \Monkey::app()->router->request()->getRequestData('translated');
        if ('con' == $translated) {
            $datatable->addIgnobleCondition('langIdTranslated', '%' . $langId . '%', false);
        } elseif ('senza' == $translated) {
            $datatable->addIgnobleCondition('langIdTranslated', '%' . $langId . '%', true);
        }

        $pnRepo = \Monkey::app()->repoFactory->create('ProductName');

        $query = $datatable->getQuery();
        $params = $datatable->getParams();


        $productsName = $pnRepo->em()->findBySql($query,$params);
        $count = $this->em->productsName->findCountBySql($query, $params);
        $totalCount = $this->em->productsName->findCountBySql($datatable->getQuery('full'), $params);

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $i = 0;

        foreach($productsName as $val){

            $pnTranslated = $pnRepo->findOneBy(['name' => $val->name, 'langId' => $langId]);
            $translated = ($pnTranslated) ? trim($pnTranslated->translation) : '';
            $name = '<div class="form-group form-group-default full-width">';
            if ($okManage) {
                $name .= '<input type="text" style="width: 100%" class="form-control full-width nameId" data-lang="' . $langId . '" data-action="' . $this->urls['base'] . 'xhr/NameTranslateLangListAjaxController" data-name="' . $val->name . '" title="nameId" class="nameId" value="' . htmlentities($translated) . '"/>';
            }
            $name .= '</div>';

            $response['data'][$i]["DT_RowId"] = 'row__' . $val->id;
            $response['data'][$i]["DT_RowClass"] = 'colore';
            $response['data'][$i]['trans'] = $name;

            $res = \Monkey::app()->dbAdapter->query(
                "SELECT `p`.`id` as `productId`, `p`.`productVariantId` FROM ((ProductNameTranslation as `pn` JOIN Product as `p` ON `p`.`productVariantId` = `pn`.`productVariantId`) JOIN `ProductStatus` as `ps` ON `p`.`productStatusId` = `ps`.`id`) WHERE `langId` = 1 AND `pn`.`name` = ? AND `ps`.`code` in ('A', 'P', 'I') AND (`p`.`qty` > 0) AND (`p`.`dummyPicture` NOT LIKE '%bs-dummy%')",
                str_replace(' !', '', [$val->name]))->fetchAll();
            $response['data'][$i]['count'] = count($res); //$products->count();

            $iterator = 0;
            $cats = [];
            foreach($res as $v) {
                if (10 == $iterator) break;
                $p = \Monkey::app()->repoFactory->create('Product')->findOneBy(['id' => $v['productId'], 'productVariantId' => $v['productVariantId']]);
                foreach($p->productCategoryTranslation as $cat) {
                    $path = $this->app->categoryManager->categories()->getPath($cat->productCategoryId);
                    unset($path[0]);
                    $newCat = '<span class="small">'.implode('/',array_column($path, 'slug')).'</span><br />';
                    if (in_array($newCat, $cats)) continue;
                    $cats[] = $newCat;
                    $iterator++;
                    if (10 == $iterator) break;
                }
            }
            $response['data'][$i]['category'] = implode('', $cats);
            $response['data'][$i]['name'] = $val->name;
            $i++;
        }
        return json_encode($response);
    }

    public function put()
    {
        $name = trim(\Monkey::app()->router->request()->getRequestData('name'));
        $translated = trim(\Monkey::app()->router->request()->getRequestData('translated'));
        if ("" == $translated) return false;
        $langId = \Monkey::app()->router->request()->getRequestData('lang');

        $pnRepo = \Monkey::app()->repoFactory->create('ProductName');
        $pntRepo = \Monkey::app()->repoFactory->create('ProductNameTranslation');

        $pn = $pnRepo->findOneBy(['name' => $name, 'langId' => 1]);
        if (!$pn) throw new BambooException('OOPS! Non si può inserire una traduzione se non esiste il nome in italiano');

        \Monkey::app()->repoFactory->beginTransaction();
        try {
            $pntRepo->insertTranslation($name, $langId, $translated);
            \Monkey::app()->repoFactory->commit();
            return true;
        }  catch (\Throwable $e) {
            \Monkey::app()->repoFactory->rollback();
           return $e->getMessage();
        }
    }
}